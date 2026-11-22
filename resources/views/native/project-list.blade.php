<native:bottom-nav label-visibility="labeled">
    <native:bottom-nav-item
        id="audio"
        icon="mic"
        label="Audio"
        url="/"
        active
    />

    <native:bottom-nav-item
        id="record"
        icon="videocam"
        label="Record"
        url="/record"
    />

    <native:bottom-nav-item
        id="more"
        icon="ellipsis-horizontal"
        label="More"
        url="/more"
    />
</native:bottom-nav>

{{-- Screen content --}}
<native:column class="w-full h-full p-4 gap-3 safe-area">
    <native:text class="text-2xl font-bold text-teal-400">Audio</native:text>

    <native:outlined-text-input native:model="script" label="Write script" />

    <native:column class="w-full p-4 gap-2 bg-theme-surface rounded-xl">
        @forelse($this->visibleLines as $i => $line)
            <native:text class="{{ $i === 0 ? 'text-2xl font-bold text-teal-400' : 'text-xl text-white' }}">{{ $line }}</native:text>
        @empty
            <native:text class="text-gray-400">Your script will appear here</native:text>
        @endforelse
    </native:column>

    <native:text class="text-gray-400">{{ $status }}</native:text>

    @if($recording)
        <native:button label="Stop" icon="stop" @press="stopAudio" />
    @else
        <native:button label="Start Audio" icon="mic" @press="startAudio" />
    @endif
</native:column>
