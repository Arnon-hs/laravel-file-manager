@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Upload files</div>

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
                        {!! Form::open(array('style' => 'display: inline-block;', 'enctype'=> 'multipart/form-data', 'route' => 'manager.file.store')) !!}
                            {!! Form::label('Directory', 'Directory') !!}
                            {!! Form::select('directory', $directories, null, array('class' => 'form-control')) !!}
                            {!! Form::open(array('files'=>'true')) !!}
                            {!! Form::file('filenames[]', array('multiple' => true)) !!}
                            {!! Form::submit('Upload', array('class' => 'btn btn-xs btn-info')) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
