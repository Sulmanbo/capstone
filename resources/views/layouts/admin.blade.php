{{--
  layouts/admin.blade.php

  Compatibility shim — many `admin/registrars/*` views extend `layouts.admin`
  while the rest of the project extends `layouts.app`. Rather than rewriting
  every legacy view, this layout simply forwards to layouts.app.

  Any view extending `layouts.admin` is rendered exactly as if it extended
  `layouts.app`. Blade sections (`@section('title')`, `@section('content')`,
  `@section('breadcrumb')`) are preserved automatically because Blade's
  section storage is global within a single render pass.
--}}
@extends('layouts.app')
