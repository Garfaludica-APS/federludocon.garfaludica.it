@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', $exception->getStatusCode())
@section('message', __('Server Error'))
