<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/js/app.js'])
</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <div class="h-screen flex flex-col" x-data="chat">
        <div class="bg-gray-200 flex-1 overflow-y-scroll">
            <div class="px-4 py-2">
                <template x-for="message in messages">
                    <div class="bg-white rounded-lg p-2 shadow mb-2 max-w-full w-fit flex items-center gap-4">
                        <span x-text="message.text"></span>
                        <span class="text-gray-400 flex items-center gap-1 text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M5 19h1.425L16.2 9.225L14.775 7.8L5 17.575zm-1 2q-.425 0-.712-.288T3 20v-2.425q0-.4.15-.763t.425-.637L16.2 3.575q.3-.275.663-.425t.762-.15t.775.15t.65.45L20.425 5q.3.275.437.65T21 6.4q0 .4-.138.763t-.437.662l-12.6 12.6q-.275.275-.638.425t-.762.15zM19 6.4L17.6 5zm-3.525 2.125l-.7-.725L16.2 9.225z" />
                            </svg>
                            <span x-text="message.created_at"></span>
                        </span>
                    </div>
                </template>
            </div>
        </div>
        <form class="bg-gray-100 px-4 py-2" @submit.prevent="sendMessage">
            <div class="flex items-center">
                <input class="w-full border rounded-full py-2 px-4 mr-2" type="text" placeholder="Type your message..." x-model="message">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-full">
                    Send
                </button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("chat", () => ({
                message: "",
                messages: @json($messages),
                init() {
                    window.addEventListener("message.sent", (e) => {
                        this.messages = e.detail.messages;
                    });
                },
                async sendMessage() {
                    if (!this.message.trim()) {
                        return;
                    }
                    const res = await fetch("{{ route('send-message') }}", {
                        method: "POST",
                        body: JSON.stringify({
                            text: this.message
                        }),
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.message = "";
                    }
                }
            }))
        });
    </script>
    <script type="module">
        Echo.channel("chat")
            .listen(".message.sent", (response) => {
                var event = new CustomEvent('message.sent', {
                    detail: {
                        messages: response.messages
                    }
                });
                window.dispatchEvent(event);
            })
        // .listenForWhisper("typing", (response) => {
        //     isFriendTyping.value = response.userID === props.friend.id;

        //     if (isFriendTypingTimer.value) {
        //         clearTimeout(isFriendTypingTimer.value);
        //     }

        //     isFriendTypingTimer.value = setTimeout(() => {
        //         isFriendTyping.value = false;
        //     }, 1000);
        // });
    </script>
</body>

</html>