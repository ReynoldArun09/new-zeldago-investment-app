@extends('user.layouts.app')

@section('title', 'Genealogy Tree')

@section('content')
<style>
    /* Basic Tree CSS */
    .genealogy-tree {
        display: flex;
        justify-content: center;
        overflow-x: auto;
        padding-bottom: 2rem;
    }
    .genealogy-tree ul {
        display: flex;
        padding-top: 20px; 
        position: relative;
        transition: all 0.5s;
    }
    .genealogy-tree li {
        float: left; text-align: center;
        list-style-type: none;
        position: relative;
        padding: 20px 5px 0 5px;
        transition: all 0.5s;
    }
    .genealogy-tree li::before, .genealogy-tree li::after{
        content: '';
        position: absolute; top: 0; right: 50%;
        border-top: 2px solid var(--primary);
        width: 50%; height: 20px;
    }
    .genealogy-tree li::after{
        right: auto; left: 50%;
        border-left: 2px solid var(--primary);
    }
    .genealogy-tree li:only-child::after, .genealogy-tree li:only-child::before {
        display: none;
    }
    .genealogy-tree li:only-child{ padding-top: 0;}
    .genealogy-tree li:first-child::before, .genealogy-tree li:last-child::after{
        border: 0 none;
    }
    .genealogy-tree li:last-child::before{
        border-right: 2px solid var(--primary);
        border-radius: 0 5px 0 0;
    }
    .genealogy-tree li:first-child::after{
        border-radius: 5px 0 0 0;
    }
    .genealogy-tree ul ul::before{
        content: '';
        position: absolute; top: 0; left: 50%;
        border-left: 2px solid var(--primary);
        width: 0; height: 20px;
    }
    .member-card {
        border: 2px solid var(--primary);
        padding: 12px 18px;
        border-radius: 12px;
        display: inline-block;
        background-color: white;
        min-width: 140px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }
    .member-card.current-user {
        background-color: var(--primary);
        color: white;
        transform: scale(1.05);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    }
    .member-card.current-user .member-name,
    .member-card.current-user .member-username {
        color: white;
    }
    .member-card.current-user .member-avatar {
        background-color: white;
        color: var(--primary);
    }
    .member-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .member-card.current-user:hover {
        transform: translateY(-4px) scale(1.05);
    }
    .member-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1f2937;
        margin-top: 8px;
    }
    .member-username {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 2px;
    }
    .member-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-weight: bold;
        font-size: 1.1rem;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Genealogy Tree</h1>
    <p class="text-gray-600 mt-1">A visual representation of your complete downline.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden">
    <div class="genealogy-tree">
        <ul>
            <li>
                <div class="member-card current-user">
                    <div class="member-avatar">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="member-name">{{ $user->name }}</div>
                    <div class="member-username">{{ $user->username }} (You)</div>
                </div>
                @if($user->downline->count() > 0)
                    <ul>
                        @foreach($user->downline as $child)
                            @include('user.network.partials.tree_node', ['member' => $child])
                        @endforeach
                    </ul>
                @endif
            </li>
        </ul>
    </div>
</div>
@endsection
