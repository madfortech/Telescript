<native:bottom-nav label-visibility="labeled">
    <native:bottom-nav-item
        id="audio"
        icon="mic"
        label="Audio"
        url="/"
    />

    <native:bottom-nav-item
        id="record"
        icon="videocam"
        label="Record"
        url="/record"
        active
    />  

    <native:bottom-nav-item
        id="more"
        icon="ellipsis-horizontal"
        label="More"
        url="/more"
    />
</native:bottom-nav>

<native:column class="w-full h-full p-4 gap-2 safe-area">
    <native:text class="text-2xl font-bold text-teal-400">Video</native:text>

    <native:outlined-text-input native:model="script" label="Write script" multiline="true" class="h-32" />

    <native:column class="w-full p-3 gap-1 bg-theme-surface rounded-xl">
        @forelse(array_slice($this->scriptLines, 0, 3) as $line)
            <native:text class="text-sm text-white">{{ $line }}</native:text>
        @empty
            <native:text class="text-gray-400">Your script will appear here</native:text>
        @endforelse
        @if(count($this->scriptLines) > 3)
            <native:text class="text-xs text-gray-400">+{{ count($this->scriptLines) - 3 }} more lines</native:text>
        @endif
    </native:column>

    <native:text class="text-gray-400">{{ $recordingStatus }}</native:text>

    @if($pendingVideoPath)
        <native:text class="text-xs text-amber-400">Video saves to gallery only. No in-app copy is kept; app data resets after saving.</native:text>
        <native:button label="Save to Gallery" icon="save" @press="savePendingVideo" />
        <native:button label="Discard" icon="trash" @press="discardPendingVideo" />
    @else
        <native:button label="Start Video" icon="videocam" @press="startRecording" />
    @endif
</native:column>

