<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen KosanKu</title>
    @vite(['resources/css/app.css', 'resource/js/app.js'])
</head>
<body class="flex h-screen bg-background font-sans overflow-hidden">
    <aside class="w-56 shrink-0 bg-sidebar flex flex-col h-full">
        <div class="px-5 py-5 border-b border-sidebar-border">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">house</title><g fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></g></svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-white leading-none">KosanKu</div>
                    <div class="text-[10px] text-sidebar-foreground/50 mt-0.5">Manajemen Kosan</div>
                </div>
            </div>
        </div>

        <div class="px-3 py-3 border-b border-sidebar-border">
            <div class="flex items-center gap-2 px-3 py-2 bg-sidebar-accent rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32"><title xmlns="">search</title><g fill="none" stroke="#737373" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="14" cy="14" r="12"/><path d="m23 23l7 7"/></g></svg>
                <span class="text-xs text-sidebar-foreground/40">Cari...</span>
            </div>
        </div>

        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-sidebar-foreground/30 px-2 mb-2">Menu Utama</p>
            <a href=""
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground text-sm font-medium transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">dashboard-outline-rounded</title><path fill="currentColor" d="M13 8V4q0-.425.288-.712T14 3h6q.425 0 .713.288T21 4v4q0 .425-.288.713T20 9h-6q-.425 0-.712-.288T13 8M3 12V4q0-.425.288-.712T4 3h6q.425 0 .713.288T11 4v8q0 .425-.288.713T10 13H4q-.425 0-.712-.288T3 12m10 8v-8q0-.425.288-.712T14 11h6q.425 0 .713.288T21 12v8q0 .425-.288.713T20 21h-6q-.425 0-.712-.288T13 20M3 20v-4q0-.425.288-.712T4 15h6q.425 0 .713.288T11 16v4q0 .425-.288.713T10 21H4q-.425 0-.712-.288T3 20m2-9h4V5H5zm10 8h4v-6h-4zm0-12h4V5h-4zM5 19h4v-2H5zm4-2"/></svg>
                Dasbor
            </a>
            <a href="{{ route('branches.index')}}"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all {{ request()->routeIs('branches.*') ? 'bg-primary text-white font-medium' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground' }} text-sm font-medium transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">apartment</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.5 2.5h-3c-1.886 0-2.828 0-3.414.586S2.5 4.614 2.5 6.5v11c0 1.886 0 2.828.586 3.414s1.528.586 3.414.586h7v-15c0-1.886 0-2.828-.586-3.414S11.386 2.5 9.5 2.5m-4 3h1m3 0h1m-5 3h1m3 0h1m-5 3h1m3 0h1m-5 3h1m3 0h1m-2.5 7v-3m9.5-11h-4v14h4c1.886 0 2.828 0 3.414-.586s.586-1.528.586-3.414v-6c0-1.886 0-2.828-.586-3.414S19.386 7.5 17.5 7.5m-.5 3h1m-1 3h1m-1 3h1"/></svg>
                Cabang
            </a>
            <a href="{{ route('rooms.index')}}"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all {{ request()->routeIs('rooms.*') ? 'bg-primary text-white font-medium' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground' }} text-sm font-medium transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">door-open-outline-rounded</title><path fill="currentColor" d="M11.713 12.713Q12 12.425 12 12t-.288-.712T11 11t-.712.288T10 12t.288.713T11 13t.713-.288M7 21v-2l6-1V6.875q0-.375-.225-.675t-.575-.35L7 5V3l5.5.9q1.1.2 1.8 1.025T15 6.85v11.1q0 .725-.475 1.288t-1.2.687zm0-2h10V5H7zm-3 2q-.425 0-.712-.288T3 20t.288-.712T4 19h1V5q0-.825.588-1.412T7 3h10q.825 0 1.413.588T19 5v14h1q.425 0 .713.288T21 20t-.288.713T20 21z"/></svg>
                Kamar
            </a>
            <a href="{{ route('tenants.index') }}"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all {{ request()->routeIs('tenants.*') ? 'bg-primary text-white font-medium' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground' }} text-sm font-medium transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 15 15"><title xmlns="">people</title><path fill="currentColor" d="M5 8.9c1.44 0 2.68.252 3.575.855C9.502 10.38 10 11.343 10 12.6a.501.501 0 0 1-1 0c0-.958-.358-1.596-.983-2.017C7.359 10.141 6.35 9.9 5 9.9s-2.36.241-3.017.684C1.358 11.005 1 11.643 1 12.601a.501.501 0 0 1-1 0c0-1.258.497-2.221 1.424-2.846C2.319 9.152 3.56 8.9 5 8.9m4.975 0c1.439 0 2.68.252 3.575.855c.927.625 1.425 1.588 1.425 2.846a.5.5 0 0 1-1 0c0-.958-.358-1.596-.984-2.017c-.518-.349-1.253-.57-2.202-.65a4.5 4.5 0 0 0-.87-1.033zM5 1.85a3.151 3.151 0 0 1 0 6.3a3.15 3.15 0 1 1 0-6.3m4.975 0a3.15 3.15 0 0 1 0 6.3c-.524 0-1.016-.13-1.45-.356a4.5 4.5 0 0 0 .534-.852a2.15 2.15 0 1 0 0-3.887a4.5 4.5 0 0 0-.535-.85a3.1 3.1 0 0 1 1.45-.355M5 2.85a2.151 2.151 0 0 0 0 4.3a2.15 2.15 0 0 0 0-4.3"/></svg>
                Penyewa
            </a>
            <a href="{{ route('kontrak.index') }}"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all {{ request()->routeIs('kontrak.*') ? 'bg-primary text-white font-medium' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground' }} text-sm font-medium transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 56 56"><title xmlns="">doc-text</title><path fill="currentColor" d="M15.555 53.125h24.89c4.852 0 7.266-2.461 7.266-7.336V24.508c0-3.024-.328-4.336-2.203-6.258L32.57 5.102c-1.78-1.829-3.234-2.227-5.882-2.227H15.555c-4.828 0-7.266 2.484-7.266 7.36v35.554c0 4.898 2.438 7.336 7.266 7.336m.187-3.773c-2.414 0-3.68-1.29-3.68-3.633V10.305c0-2.32 1.266-3.657 3.704-3.657h10.406v13.618c0 2.953 1.5 4.406 4.406 4.406h13.36v21.047c0 2.343-1.243 3.633-3.68 3.633ZM31 21.132c-.914 0-1.29-.374-1.29-1.312V7.375l13.5 13.758Zm5.625 9.985h-17.79c-.843 0-1.452.633-1.452 1.43c0 .82.61 1.453 1.453 1.453h17.789a1.43 1.43 0 0 0 1.453-1.453c0-.797-.633-1.43-1.453-1.43m0 8.18h-17.79c-.843 0-1.452.656-1.452 1.476c0 .797.61 1.407 1.453 1.407h17.789c.82 0 1.453-.61 1.453-1.407c0-.82-.633-1.476-1.453-1.476"/></svg>
                Kontrak Sewa
            </a>
            <a href="{{ route('pembayaran.index') }}"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all {{ request()->routeIs('pembayaran.*') ? 'bg-primary text-white font-medium' : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground' }} text-sm font-medium transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">card-outline</title><path fill="currentColor" fill-rule="evenodd" d="M9.944 3.25h4.112c1.838 0 3.294 0 4.433.153c1.172.158 2.121.49 2.87 1.238c.748.749 1.08 1.698 1.238 2.87c.09.673.127 1.456.142 2.363a.8.8 0 0 1 .004.23q.009.848.007 1.84v.112c0 1.838 0 3.294-.153 4.433c-.158 1.172-.49 2.121-1.238 2.87c-.749.748-1.698 1.08-2.87 1.238c-1.14.153-2.595.153-4.433.153H9.944c-1.838 0-3.294 0-4.433-.153c-1.172-.158-2.121-.49-2.87-1.238c-.748-.749-1.08-1.698-1.238-2.87c-.153-1.14-.153-2.595-.153-4.433v-.112q-.002-.992.007-1.84a.8.8 0 0 1 .003-.23c.016-.907.053-1.69.143-2.363c.158-1.172.49-2.121 1.238-2.87c.749-.748 1.698-1.08 2.87-1.238c1.14-.153 2.595-.153 4.433-.153m-7.192 7.5q-.002.582-.002 1.25c0 1.907.002 3.262.14 4.29c.135 1.005.389 1.585.812 2.008s1.003.677 2.009.812c1.028.138 2.382.14 4.289.14h4c1.907 0 3.262-.002 4.29-.14c1.005-.135 1.585-.389 2.008-.812s.677-1.003.812-2.009c.138-1.028.14-2.382.14-4.289q0-.668-.002-1.25zm18.472-1.5H2.776c.02-.587.054-1.094.114-1.54c.135-1.005.389-1.585.812-2.008s1.003-.677 2.009-.812c1.028-.138 2.382-.14 4.289-.14h4c1.907 0 3.262.002 4.29.14c1.005.135 1.585.389 2.008.812s.677 1.003.812 2.009c.06.445.094.952.114 1.539M5.25 16a.75.75 0 0 1 .75-.75h4a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1-.75-.75m6.5 0a.75.75 0 0 1 .75-.75H14a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75" clip-rule="evenodd"/></svg>
                Pembayaran
            </a>
            <a href="#"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground text-sm font-medium transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">chart-bar</title><path fill="currentColor" fill-rule="evenodd" d="M11.25 5h1.5v15h-1.5zM6 10h1.5v10H6zm12 4h-1.5v6H18z" clip-rule="evenodd"/></svg>
                Laporan
            </a>
            <a href="#"
                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground text-sm font-medium transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">setting</title><path fill="currentColor" d="M19.9 12.66a1 1 0 0 1 0-1.32l1.28-1.44a1 1 0 0 0 .12-1.17l-2-3.46a1 1 0 0 0-1.07-.48l-1.88.38a1 1 0 0 1-1.15-.66l-.61-1.83a1 1 0 0 0-.95-.68h-4a1 1 0 0 0-1 .68l-.56 1.83a1 1 0 0 1-1.15.66L5 4.79a1 1 0 0 0-1 .48L2 8.73a1 1 0 0 0 .1 1.17l1.27 1.44a1 1 0 0 1 0 1.32L2.1 14.1a1 1 0 0 0-.1 1.17l2 3.46a1 1 0 0 0 1.07.48l1.88-.38a1 1 0 0 1 1.15.66l.61 1.83a1 1 0 0 0 1 .68h4a1 1 0 0 0 .95-.68l.61-1.83a1 1 0 0 1 1.15-.66l1.88.38a1 1 0 0 0 1.07-.48l2-3.46a1 1 0 0 0-.12-1.17ZM18.41 14l.8.9l-1.28 2.22l-1.18-.24a3 3 0 0 0-3.45 2L12.92 20h-2.56L10 18.86a3 3 0 0 0-3.45-2l-1.18.24l-1.3-2.21l.8-.9a3 3 0 0 0 0-4l-.8-.9l1.28-2.2l1.18.24a3 3 0 0 0 3.45-2L10.36 4h2.56l.38 1.14a3 3 0 0 0 3.45 2l1.18-.24l1.28 2.22l-.8.9a3 3 0 0 0 0 3.98m-6.77-6a4 4 0 1 0 4 4a4 4 0 0 0-4-4m0 6a2 2 0 1 1 2-2a2 2 0 0 1-2 2"/></svg>
                Pengaturan
            </a>

            <div class="pt-4 pb-1">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-sidebar-foreground/30 px-2 mb-2">Fasilitas</p>
                <a href="#"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground text-sm font-medium transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">wifi</title><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 18h.01m-2.838-2.828a4 4 0 0 1 5.656 0m-8.485-2.829a8 8 0 0 1 11.314 0"/><path d="M3.515 9.515c4.686-4.687 12.284-4.687 17 0"/></g></svg>
                    WiFi
                </a>
                <a href="#"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground text-sm font-medium transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">water-drops-outline-rounded</title><path fill="currentColor" d="M17.5 6.044q-.963 1.033-1.482 1.874t-.518 1.39q0 .87.566 1.531q.565.661 1.43.661q.866 0 1.435-.661q.569-.66.569-1.531q0-.548-.518-1.39T17.5 6.044M11.997 21q-2.999 0-4.998-2.064T5 13.8q0-2.042 1.551-4.472t4.697-5.336q.323-.292.749-.302t.75.283q.953.873 1.738 1.703q.784.83 1.457 1.628q-.078.11-.326.394q-.247.286-.316.377q-.675-.8-1.487-1.662T12 4.65Q9.025 7.375 7.513 9.675T6 13.8q0 2.675 1.7 4.438T12 20q1.3 0 2.4-.475t1.9-1.3t1.25-1.963T18 13.8q0-.425-.088-.9t-.262-.975q.108-.074.426-.283t.426-.282q.256.648.377 1.256T19 13.8q0 3.073-2.002 5.137Q14.994 21 11.996 21m.22-2.308q.224-.025.35-.153q.125-.13.125-.311q0-.212-.143-.329q-.144-.116-.368-.091q-1.025.075-2.29-.64q-1.266-.714-1.566-2.39q-.05-.236-.166-.353q-.117-.117-.295-.117q-.197 0-.335.147t-.083.44q.387 2.082 1.923 2.98t2.848.817m3.149-7.138q-.866-.946-.866-2.258q0-.83.618-1.85T17.04 5.14q.202-.186.465-.186t.455.186q1.34 1.318 1.94 2.294t.6 1.868q0 1.306-.865 2.252q-.866.946-2.135.946t-2.134-.946m-3.397.771"/></svg>
                    Air
                </a>
                <a href="#"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-all text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground text-sm font-medium transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20"><title xmlns="">electricity</title><path fill="currentColor" fill-rule="evenodd" d="M15 8.5h-3.813l2.273-5.303A.5.5 0 0 0 13 2.5H8a.5.5 0 0 0-.46.303l-3 7A.5.5 0 0 0 5 10.5h2.474l-2.938 7.314c-.2.497.417.918.807.55l5.024-4.743l4.958-4.241A.5.5 0 0 0 15 8.5m-4.571 1h3.217l-3.948 3.378l-3.385 3.195l2.365-5.887a.5.5 0 0 0-.464-.686H5.758l2.572-6h3.912L9.969 8.803a.5.5 0 0 0 .46.697" clip-rule="evenodd"/></svg>
                    Listrik
                </a>
            </div>
        </nav>

        <div class="px-3 py-3 border-t border-sidebar-border">
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-lg hover:bg-sidebar-accent cursor-pointer transition-all">
                <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-xs font-semibold text-primary">RJ</div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-medium text-sidebar-foreground truncate">Renggana Jaya</div>
                    <div class="text-[10px] text-sidebar-foreground/40 truncate">Pemilik</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="#737373"><title xmlns="" fill="#737373">arrow-ios-downward-outline</title><path fill="#737373" d="M12 16a1 1 0 0 1-.64-.23l-6-5a1 1 0 1 1 1.28-1.54L12 13.71l5.36-4.32a1 1 0 0 1 1.41.15a1 1 0 0 1-.14 1.46l-6 4.83A1 1 0 0 1 12 16"/></svg>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="h-14 bg-card border-b border-border flex items-center px-6 gap-4 shrink-0">
            <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                <span>KosanKu</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">arrow-ios-forward-outline</title><path fill="#737373" d="M10 19a1 1 0 0 1-.64-.23a1 1 0 0 1-.13-1.41L13.71 12L9.39 6.63a1 1 0 0 1 .15-1.41a1 1 0 0 1 1.46.15l4.83 6a1 1 0 0 1 0 1.27l-5 6A1 1 0 0 1 10 19"/></svg>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <span class="text-xs text-muted-foreground hidden sm:block">📅 18 Juli 2026</span>
                <button class="relative w-8 h-8 flex items-center justify-center rounded-lg hover:bg-muted transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><title xmlns="">bell</title><path fill="none" stroke="#737373" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.268 21a2 2 0 0 0 3.464 0m-10.47-5.674A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" />
                </button>
                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-semibold text-primary cursor-pointer">RJ</div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>
</html>
