@extends('errors::minimal')

@section('title', __('Bad Request'))
@section('code', $exception->getStatusCode())
@section('message', __('Bad Request'))
