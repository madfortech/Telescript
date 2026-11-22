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
    />

    <native:bottom-nav-item
        id="more"
        icon="expand_more"
        label="More"
        url="/more"
        active
    />
</native:bottom-nav>

<native:column class="w-full h-full safe-area p-4 gap-4">
    <native:text class="text-2xl font-bold text-teal-400">
        More
    </native:text>

    <native:column class="w-full gap-4">
        <native:button
            label="About TeleScript"
            variant="secondary"
            icon="info"
            @press="openAbout"
        />

        <native:button
            label="Privacy Policy"
            variant="secondary"
            icon="info"
            @press="openPrivacy"
        />

        <native:button
            label="Terms of Service"
            variant="secondary"
            icon="info"
            @press="openTerms"
        />
        
    </native:column>
</native:column>
