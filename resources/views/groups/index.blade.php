@extends('layouts.app')

@section('title', 'Groups')

@section('content')
    <section class="container">
        @component('components.heading', ['title' => 'Groups', 'url' => route('groups.index')])
            <a href="{{ route('groups.create') }}" class="btn btn-primary btn-pill mr-2">New group</a>

            {!! Form::open(['route' => 'groups.index', 'method' => 'GET']) !!}
                {!! Form::search('filter[name]', null, ['placeholder' => 'Filter', 'class' => 'field']) !!}
                {!! Form::submit('Search', ['class' => 'btn btn-primary screen-reader']) !!}
            {!! Form::close() !!}
        @endcomponent

        <table class="table" cellspacing="0">
            <thead>
                <th class="table-column-check">#</th>
                <th><a href="{{ route('groups.index', ['sort' => (request('sort') === 'name' || request()->has('sort') === false ? '-' : '') . 'name'] + request()->except('page')) }}">Name</a></th>
                <th>&nbsp;</th>
            </thead>

            <tbody>
                @forelse($groups as $group)
                    <tr>
                        <td data-title="&nbsp;"></td>
                        <td data-title="Name">
                            <a href="{{ route('groups.edit', $group) }}">{{ $group->name }}</a>
                        </td>
                        <td data-title="&nbsp;" class="table-actions">
                            {!! Form::model($group, ['route' => ['groups.destroy', $group->id], 'method' => 'DELETE' ]) !!}
                            <button type="submit" title="Delete">
                                @svg('trash', 'w-5')
                            </button>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No groups was found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $groups->appends(['filter' => request()->get('filter'), 'sort' => request()->get('sort')])->links() }}
    </section>
@endsection
