@extends('layouts.app')
@section('title', 'New Schedule')
@section('breadcrumb', 'New Schedule')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">New Schedule</h1>
      <p class="enc-page__subtitle">Create a class schedule. Faculty assignment is optional — schedules without a teacher remain as TBA.</p>
    </div>
  </div>
</div>

@include('admin.schedules._form', [
  'action'       => route('admin.schedules.store'),
  'method'       => 'POST',
  'formTitle'    => 'Schedule Details',
  'submitLabel'  => 'Create Schedule',
  'reloadRoute'  => route('admin.schedules.create'),
  'schedule'     => null,
])

@endsection
