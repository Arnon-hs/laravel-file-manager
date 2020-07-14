@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <span>List of files</span>
                        <span style="margin-left: 1rem;">
                            <a href="{{route('manager.file.upload')}}">
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-file-earmark-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M9 1H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h5v-1H4a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h5v2.5A1.5 1.5 0 0 0 10.5 6H13v2h1V6L9 1z"/>
                                  <path fill-rule="evenodd" d="M13.5 10a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1H13v-1.5a.5.5 0 0 1 .5-.5z"/>
                                  <path fill-rule="evenodd" d="M13 12.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0v-2z"/>
                                </svg>
                                Upload files
                            </a>
                        </span>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(count($files) > 0)
                            <table class="table products">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Directory</th>
                                        <th scope="col">Size (MB)</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $key => $file)
                                        <tr>
                                            <th scope="row">{{$key+1}}</th>
                                            <td>{{$file['name']}}</td>
                                            <td>{{$file['path']}}</td>
                                            <td>{{$file['size']}}</td>
                                            <td>
                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'DELETE',
                                                    'route' => 'manager.file.delete')) !!}
                                                {!! Form::hidden('id', $file->id) !!}
                                                {!! Form::submit('Delete', array('class' => 'btn btn-sm btn-danger')) !!}
                                                {!! Form::close() !!}

                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'POST',
                                                    'route' => 'manager.file.download')) !!}
                                                {!! Form::hidden('id', $file->id) !!}
                                                {!! Form::submit('Download', array('class' => 'btn btn-sm btn-success')) !!}
                                                {!! Form::close() !!}

                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'POST',
                                                    'route' => 'manager.file.show')) !!}
                                                {!! Form::hidden('id', $file->id) !!}
                                                {!! Form::submit('Show', array('class' => 'btn btn-sm btn-primary')) !!}
                                                {!! Form::close() !!}
                                                <div class="btn-group dropup">
                                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Rename
                                                    </button>
                                                    <div class="dropdown-menu file rename">
                                                        {!! Form::open(array(
                                                            'style' => 'display: inline-block;',
                                                            'method' => 'POST',
                                                            'route' => 'manager.file.rename')) !!}
                                                        {!! Form::hidden('id', $file->id) !!}
                                                        {!! Form::label('newName', 'Enter a new file name without special characters') !!}
                                                        {!! Form::text('newName') !!}
                                                        {!! Form::submit('Submit', array('class' => 'btn btn-sm btn-warning')) !!}
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                                @if($file->public_link)
                                                <div class="btn-group dropup">
                                                    <button type="button" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Public link
                                                    </button>
                                                    <div class="dropdown-menu file public-link">
                                                        {!! Form::open(array(
                                                            'style' => 'display: inline-block;',
                                                            'method' => 'POST',
                                                            'route' => 'manager.file.publicLink')) !!}
                                                        {!! Form::hidden('id', $file->id) !!}
                                                        {!! Form::label('public_link', 'Share a public link to the file') !!}
                                                        {!! Form::text('public_link', $file->public_link, ['readonly']) !!}
                                                        {!! Form::submit('Submit', array('class' => 'btn btn-sm btn-warning')) !!}
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                                @else
                                                    {!! Form::open(array(
                                                            'style' => 'display: inline-block;',
                                                            'method' => 'POST',
                                                            'route' => 'manager.file.publicLink')) !!}
                                                    {!! Form::hidden('id', $file->id) !!}
                                                    {!! Form::submit('Public link', array('class' => 'btn btn-sm btn-dark')) !!}
                                                    {!! Form::close() !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $files->render() }}
                        @else
                                <div><h2 class="text-center">Here is empty so far, upload files</h2></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
