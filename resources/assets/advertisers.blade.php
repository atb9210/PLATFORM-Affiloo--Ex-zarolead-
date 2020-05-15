@extends('layouts.app')

@section('content')
<div class="flex-center position-ref">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    @endif

    <div class="content">
        <nav aria-label="breadcrumb" class="nav-breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Advertisers</li>
        </ol>
        </nav>
        <h1 class="page-title">Advertisers</h1>
        <div class="actions">
        	<a href="{{ route('advertiser/create') }}" class="btn btn-primary">Create New</a>
			<button class="btn btn-danger" onclick="deleteSelectedObjects();">Delete</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                	<th scope="col"><input type="checkbox" onclick="selectAllObjects();"></th>
                    <th scope="col">ID</th>
                	<th scope="col">Creation Date</th>
                	<th scope="col">Update Date</th>
                    <th scope="col">Name</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($objects as $object)
                    <tr>
                    	<td><input type="checkbox" id="select_{{ $object->id }}"></td>
                        <td>{{ $object->id }}</td>
                    	<td>{{ $object->created_at }}</td>
                    	<td>{{ $object->updated_at }}</td>
                        <td>{{ $object->name }}</td>
                        <td>
                            <?php
                                $edit_route = route('advertiser/edit', ['id' => $object->id]);
                            ?>
                            <a href="{{ $edit_route }}" class="btn btn-primary" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="#" class="btn btn-danger" onclick="deleteObject({{ $object->id }});" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
var objects = [<?php foreach($objects as $object) { echo $object->id . ", "; } ?>];
var object_name = 'advertiser';
var selectedObjects = [];
var deletedObjects = 0;
var allObjectsSelected = false;

function refreshSelectedObjects() {
    selectedObjects = [];
    for (iIndex in objects) {
        var object = objects[iIndex];
        if ($('#select_' + object).is(":checked")) {
            selectedObjects.push(object);
        }
    }
}

function deleteObject(object_id) {
    var url = window.location.origin + '/' + object_name + '/' + object_id;
    $.ajax({
        url: url,
        type: 'DELETE',
        data: {
            "id": object_id,
            "_method": 'DELETE',
            "_token": '{{ csrf_token() }}',
        },
        success: function(data) {
            location.reload();
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function deleteSelectedObjects() {
    refreshSelectedObjects();

    for (iIndex in selectedObjects) {
        var object_id = selectedObjects[iIndex];
        var url = window.location.origin + '/' + object_name + '/' + object_id;
        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                "id": object_id,
                "_method": 'DELETE',
                "_token": '{{ csrf_token() }}',
            },
            success: function(data) {
                deletedObjects += 1;
                if (deletedObjects == selectedObjects.length) {
                    location.reload();
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
}

function selectAllObjects() {
    if (!allObjectsSelected) {
        for (iIndex in objects) {
            var object = objects[iIndex];
            $('#select_' + object).prop("checked", true);
        }
        allObjectsSelected = true;
    } else {
        for (iIndex in objects) {
            var object = objects[iIndex];
            $('#select_' + object).prop("checked", false);
        }
        allObjectsSelected = false;
    }
}
</script>
@endsection