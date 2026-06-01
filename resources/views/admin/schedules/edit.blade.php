@extends('layouts.app')
@section('title', 'Edit Schedule')
@section('breadcrumb', 'Edit Schedule')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Edit Schedule</h1>
      <p class="enc-page__subtitle">Update class schedule details. Conflict checks run on save.</p>
    </div>
  </div>
</div>

@include('admin.schedules._form', [
  'action'       => route('admin.schedules.update', $schedule),
  'method'       => 'PUT',
  'formTitle'    => 'Schedule Details',
  'submitLabel'  => 'Save Changes',
  'reloadRoute'  => route('admin.schedules.edit', $schedule),
  'schedule'     => $schedule,
])

@endsection
