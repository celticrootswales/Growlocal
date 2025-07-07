@extends('layouts.app')
@section('content')

<h1>Welcome to GrowLocal!</h1>
<p>Your account has been created.</p>
<p>Email: {{ $user->email }}</p>
<p>Password: {{ $password }}</p>