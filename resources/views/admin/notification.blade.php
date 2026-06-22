@extends('admin.layouts.layout')
@section('content')


<form method="POST"
    action="{{ route('admin.notification.send') }}">

    @csrf

    <input type="text"
        name="title"
        placeholder="Title">

    <textarea
        name="message"
        placeholder="Message">
    </textarea>

    <button type="submit">
        Send To All Users
    </button>

</form>
@endsection