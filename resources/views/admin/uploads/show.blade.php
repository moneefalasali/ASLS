@extends('admin.layout')
@section('title','Upload')
@section('content')
<div>
  <p>Original: {{ $upload->original_name }}</p>
  <p>Transcription: {{ $upload->transcription }}</p>
  <audio controls src="{{ $upload->path }}"></audio>
</div>
@endsection
