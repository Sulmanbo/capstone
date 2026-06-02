<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\Notification;
use App\Models\SectionSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // ── STUDENT ───────────────────────────────────────────────────────────

    /**
     * Student inbox: received messages + sent messages tab.
     */
    public function studentInbox(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->input('tab', 'inbox');

        $inbox = Message::with('sender')
            ->where('recipient_id', $user->id)
            ->whereNull('parent_id')  // top-level threads only
            ->latest()
            ->get();

        $sent = Message::with('recipient')
            ->where('sender_id', $user->id)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        // Build the list of faculty the student can message:
        // adviser + all subject teachers from their active enrollment.
        $recipients = $this->getStudentRecipients($user);

        $unreadCount = $inbox->filter(fn($m) => is_null($m->read_at))->count();

        return view('dashboard.student-inbox', compact('inbox', 'sent', 'recipients', 'tab', 'unreadCount'));
    }

    /**
     * Student sends a new message to a faculty member.
     */
    public function studentStore(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'subject'      => ['required', 'string', 'max:255'],
            'body'         => ['required', 'string', 'max:3000'],
        ]);

        // Make sure the chosen recipient is actually one of their teachers.
        $allowed = $this->getStudentRecipients($user)->pluck('id')->all();
        if (! in_array((int) $data['recipient_id'], $allowed)) {
            return back()->withErrors(['recipient_id' => 'You can only message your assigned teachers.']);
        }

        $message = Message::create([
            'sender_id'    => $user->id,
            'recipient_id' => $data['recipient_id'],
            'subject'      => $data['subject'],
            'body'         => $data['body'],
        ]);

        // Create notification for recipient
        $recipient = User::find($data['recipient_id']);
        if ($recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'type' => 'message',
                'title' => 'New Message from ' . $user->full_name,
                'body' => $data['subject'],
            ]);
        }

        return back()->with('success', 'Message sent successfully.');
    }

    /**
     * Student reads a message (marks it read).
     */
    public function studentShow(Message $message)
    {
        $user = Auth::user();

        // Only allow the sender or recipient to view.
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403);
        }

        if ($message->recipient_id === $user->id) {
            $message->markRead();
        }

        $thread = $message->replies()->with('sender', 'recipient')->get()
            ->prepend($message->load('sender', 'recipient'));

        $recipients = $this->getStudentRecipients($user);

        return view('dashboard.student-message-show', compact('message', 'thread', 'recipients'));
    }

    // ── FACULTY ───────────────────────────────────────────────────────────

    /**
     * Faculty inbox.
     */
    public function facultyInbox(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->input('tab', 'inbox');

        $inbox = Message::with('sender')
            ->where('recipient_id', $user->id)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        $sent = Message::with('recipient')
            ->where('sender_id', $user->id)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        $unreadCount = $inbox->filter(fn($m) => is_null($m->read_at))->count();

        return view('dashboard.faculty-inbox', compact('inbox', 'sent', 'tab', 'unreadCount'));
    }

    /**
     * Faculty reads a message and can reply.
     */
    public function facultyShow(Message $message)
    {
        $user = Auth::user();

        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403);
        }

        if ($message->recipient_id === $user->id) {
            $message->markRead();
        }

        $thread = $message->replies()->with('sender', 'recipient')->get()
            ->prepend($message->load('sender', 'recipient'));

        return view('dashboard.faculty-message-show', compact('message', 'thread'));
    }

    /**
     * Faculty replies to a message.
     */
    public function facultyReply(Request $request, Message $message)
    {
        $user = Auth::user();

        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ]);

        // Reply goes to the other party.
        $recipientId = $message->sender_id === $user->id
            ? $message->recipient_id
            : $message->sender_id;

        $reply = Message::create([
            'sender_id'    => $user->id,
            'recipient_id' => $recipientId,
            'parent_id'    => $message->id,
            'subject'      => 'Re: ' . $message->subject,
            'body'         => $data['body'],
        ]);

        // Create notification for recipient
        $recipient = User::find($recipientId);
        if ($recipient) {
            Notification::create([
                'user_id' => $recipient->id,
                'type' => 'message',
                'title' => 'Reply from ' . $user->full_name,
                'body' => 'Re: ' . $message->subject,
            ]);
        }

        return back()->with('success', 'Reply sent.');
    }

    // ── HELPER ────────────────────────────────────────────────────────────

    /**
     * Returns the unique faculty members a student is allowed to message:
     * their homeroom adviser + all subject teachers in their active section.
     */
    private function getStudentRecipients(User $student)
    {
        $enrollment = Enrollment::with(['section.adviser', 'section.sectionSubjects.faculty'])
            ->where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->latest()
            ->first();

        if (! $enrollment) {
            return collect();
        }

        $ids = collect();

        // Homeroom adviser
        if ($enrollment->section?->adviser_id) {
            $ids->push($enrollment->section->adviser_id);
        }

        // Subject teachers
        foreach ($enrollment->section->sectionSubjects ?? [] as $ss) {
            if ($ss->faculty_id) {
                $ids->push($ss->faculty_id);
            }
        }

        $ids = $ids->unique()->values();

        return User::whereIn('id', $ids)->where('status', 'active')->orderBy('last_name')->get();
    }
}
