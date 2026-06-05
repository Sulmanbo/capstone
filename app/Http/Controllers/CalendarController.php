<?php
namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year',  now()->year);

        $events = CalendarEvent::whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->orWhere(function ($q) use ($year, $month) {
                $q->whereYear('end_date', $year)->whereMonth('end_date', $month);
            })
            ->orderBy('start_date')
            ->get();

        // Build calendar grid
        $firstDay   = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDow   = $firstDay->dayOfWeek; // 0=Sun

        // upcoming events (next 30 days)
        $upcoming = CalendarEvent::where('start_date', '>=', today())
            ->where('start_date', '<=', today()->addDays(30))
            ->orderBy('start_date')
            ->limit(8)
            ->get();

        $prevMonth = $month === 1 ? ['month' => 12, 'year' => $year - 1] : ['month' => $month - 1, 'year' => $year];
        $nextMonth = $month === 12 ? ['month' => 1,  'year' => $year + 1] : ['month' => $month + 1, 'year' => $year];

        return view('calendar.index', compact(
            'events', 'upcoming', 'month', 'year',
            'firstDay', 'daysInMonth', 'startDow',
            'prevMonth', 'nextMonth'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'type'        => 'required|in:holiday,exam,no_classes,event',
            'audience'    => 'required|in:all,student,faculty',
        ]);
        $data['created_by'] = auth()->id();
        $data['color'] = match($data['type']) {
            'holiday'    => '#ef4444',
            'exam'       => '#f59e0b',
            'no_classes' => '#8b5cf6',
            default      => '#3b82f6',
        };
        CalendarEvent::create($data);
        return back()->with('success', 'Event added to calendar.');
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $calendarEvent->delete();
        return back()->with('success', 'Event removed.');
    }
}
