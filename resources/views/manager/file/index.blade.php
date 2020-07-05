@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                       <span>List of files</span>
                        <span style="margin-left: auto;"><a href="{{route('manager.file.upload')}}">Upload files</a></span>
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
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Path</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $key => $file)
                                        <tr>
                                            <th scope="row">{{$key+1}}</th>
                                            <td>{{$file['name']}}</td>
                                            <td>{{$file['path']}}</td>
                                            <td>
                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'DELETE',
                                                    'route' => 'manager.file.delete')) !!}
                                                {!! Form::hidden('id', $file->id) !!}
                                                {!! Form::submit('Delete', array('class' => 'btn btn-xs btn-danger')) !!}
                                                {!! Form::close() !!}

                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'POST',
                                                    'route' => 'manager.file.download')) !!}
                                                {!! Form::hidden('id', $file->id) !!}
                                                {!! Form::submit('Download', array('class' => 'btn btn-xs btn-success')) !!}
                                                {!! Form::close() !!}

                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'POST',
                                                    'route' => 'manager.file.show')) !!}
                                                {!! Form::hidden('id', $file->id) !!}
                                                {!! Form::submit('Show', array('class' => 'btn btn-xs btn-primary')) !!}
                                                {!! Form::close() !!}

                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'POST',
                                                    'route' => 'manager.file.rename')) !!}
                                                {!! Form::hidden('id', $file->id) !!}
                                                {!! Form::submit('Show', array('class' => 'btn btn-xs btn-secondary')) !!}
                                                {!! Form::close() !!}
                                                {{--<a class="btn btn-secondary btn-xs" href="{{'https://pbx.smart-php.design/storage/app/'.$file->path}}">Download</a>--}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
