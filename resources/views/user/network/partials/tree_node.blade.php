<li>
    <div class="member-card">
        <div class="member-avatar">
            {{ substr($member->name, 0, 1) }}
        </div>
        <div class="member-name">{{ $member->name }}</div>
        <div class="member-username">{{ $member->username }}</div>
    </div>
    
    @if($member->downline && $member->downline->count() > 0)
        <ul>
            @foreach($member->downline as $child)
                @include('user.network.partials.tree_node', ['member' => $child])
            @endforeach
        </ul>
    @endif
</li>
