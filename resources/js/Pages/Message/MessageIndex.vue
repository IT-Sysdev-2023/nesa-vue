<template>
    <div class="flex antialiased text-gray-200 bg-gray-900 overflow-hidden">
        <div class="flex-1 flex flex-col">
            <main class="flex-grow flex flex-row min-h-0">
                <section
                    class="flex flex-col flex-none overflow-auto w-24 hover:w-64 group lg:max-w-sm md:w-2/6 transition-all duration-300 ease-in-out">
                    <div class="header p-4 flex flex-row justify-between items-center flex-none">
                        <div class="w-16 h-16 relative flex flex-shrink-0">
                            <img class="rounded-full w-full h-full object-cover" alt="ravisankarchinnam" :src="'http://172.16.161.34:8080/hrms' +
                                page.auth.user.photo
                                " />
                        </div>
                        <p class="text-md font-bold hidden md:block group-hover:block">
                            Talk To Nesa
                        </p>
                        <p @click="unexpandMessage"
                            class="block rounded-full hover:bg-gray-700 bg-gray-800 w-10 h-10 p-2 hidden md:block group-hover:block">
                            <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                <path
                                    d="M14 10V4h6v2h-4v4h-2zm-4 0H4V8h4V4h2v6zm4 4h2v4h4v2h-6v-6zm-4 0v6H4v-2h4v-4h2z" />
                            </svg>
                        </p>
                    </div>
                    <div class="search-box p-4 flex-none">
                        <div class="relative">
                            <label>
                                <input v-model="searchUsers"
                                    class="rounded-full py-2 pr-6 pl-10 w-full border border-gray-800 focus:border-gray-700 bg-gray-800 focus:bg-gray-900 focus:outline-none text-gray-200 focus:shadow-md transition duration-300 ease-in"
                                    type="text" placeholder="Search Messenger" />
                                <span class="absolute top-0 left-0 mt-2 ml-3 inline-block">
                                    <svg viewBox="0 0 24 24" class="w-6 h-6">
                                        <path fill="#bbb"
                                            d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="active-users flex flex-row p-2 overflow-auto w-0 min-w-full">
                        <div class="text-sm text-center mr-4">
                            <button @click="addNewChat"
                                class="flex flex-shrink-0 focus:outline-none block bg-gray-800 text-gray-600 rounded-full w-20 h-20"
                                type="button">
                                <svg class="w-full h-full fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M17 11a1 1 0 0 1 0 2h-4v4a1 1 0 0 1-2 0v-4H7a1 1 0 0 1 0-2h4V7a1 1 0 0 1 2 0v4h4z" />
                                </svg>
                            </button>
                            <p>Chats</p>
                        </div>
                        <div class="text-sm text-center mr-4 cursor-pointer" v-for="item in getOnlineUsers">
                            <div v-if="item.id != page.auth.user.id">

                                <div class="p-1 border-4 border-green-400 rounded-full" @click="getMesssage(item.id)">
                                    <div class="w-16 h-16 relative flex flex-shrink-0">
                                        <img class="shadow-md rounded-full w-full h-full object-cover" :src="'http://172.16.161.34:8080/hrms' +
                                            item.photo
                                            " alt="" />
                                    </div>
                                </div>
                                <p class="truncate w-[80px]">{{ item.name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="contacts p-2 flex-1 overflow-y-scroll space-y-2">
                        <div v-for="item in everyMessage" :key="item.user_id" @click="getMesssage(item.user_id)"
                            class="flex items-center justify-between p-3 rounded-xl cursor-pointer transition-all duration-200 hover:bg-gray-800"
                            :class="item.count ? 'bg-gray-900 shadow-lg' : ''">
                            <div class="w-16 h-16 relative flex justify-center items-center flex-shrink-0 rounded-full"
                                :class="item.count ? 'bg-blue-300' : ''">
                                <!-- User Photo -->
                                <img class="shadow-md rounded-full w-14 h-14" :src="'http://172.16.161.34:8080/hrms' +
                                    item.photo
                                    " alt="User2" />

                                <!-- Online Badge -->
                                <span v-if="item.isOnline"
                                    class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-gray-900 rounded-full"></span>
                                <span v-if="!item.isOnline"
                                    class="absolute bottom-1 right-1 w-4 h-4 bg-gray-400 border-2 border-gray-900 rounded-full"></span>
                            </div>

                            <!-- Message Info -->
                            <div class="flex-auto min-w-0 ml-4 mr-6">
                                <p :class="item.count
                                    ? 'font-bold text-blue-400'
                                    : 'text-gray-300'
                                    " class="truncate">
                                    {{ item.firstname }} {{ item.lastname }}
                                </p>
                                <div class="flex items-center text-sm">
                                    <p :class="item.count
                                        ? 'font-medium text-blue-400'
                                        : 'text-gray-400'
                                        " class="truncate">
                                        {{ item.message }}
                                    </p>
                                    <span class="ml-2 text-xs text-gray-500 whitespace-nowrap">
                                        {{ item.latest_at }}
                                    </span>
                                </div>
                            </div>

                            <!-- Unread Badge -->
                            <div v-if="item.count"
                                class="flex items-center animate-pulse justify-center w-4 h-4 text-xs font-bold text-white bg-blue-500 rounded-full shadow-md">
                            </div>

                            <!-- No Message or Single Unread -->
                            <div v-else-if="
                                item.message == 'no message' ||
                                item.countUnread == 1 ||
                                item.photoSeen == '0'
                            "></div>

                            <!-- Seen Photo -->
                            <div v-else>
                                <div class="w-4 h-4 flex flex-shrink-0 hidden md:block group-hover:block">
                                    <img class="rounded-full w-full h-full object-cover" alt="user2" :src="'http://172.16.161.34:8080/hrms' +
                                        item.photoSeen
                                        " />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="repId != null && !addChat" class="flex flex-col flex-auto border-l border-gray-800">
                    <!-- this is the chat section -->
                    <!-- Profile Section -->
                    <div class="chat-header px-6 py-4 flex flex-row flex-none justify-between items-center shadow">
                        <div class="flex">
                            <div>
                                <div class="w-12 h-12 mr-4 relative flex flex-shrink-0" :class="isActive[0]
                                    ? 'border rounded-full bg-green-600 p-1'
                                    : 'border rounded-full bg-gray-400 p-1'
                                    ">
                                    <img class="shadow-md rounded-full w-full h-full object-cover" :src="'http://172.16.161.34:8080/hrms' +
                                        usersPhoto
                                        " usersPhoto alt="" />
                                </div>
                            </div>
                            <div class="text-sm">
                                <p class="font-bold">{{ usersName }}</p>
                                <p>
                                    {{
                                        isActive[0]?.time
                                            ? "Active Now"
                                            : "Active " +
                                            dayjs(isOffline).toNow(true) +
                                            "ago"
                                    }}
                                </p>
                            </div>
                        </div>

                        <div class="flex">
                            <a href="#" class="block rounded-full hover:bg-gray-700 bg-gray-800 w-10 h-10 p-2">
                                <svg viewBox="0 0 20 20" class="w-full h-full fill-current text-blue-500">
                                    <path
                                        d="M11.1735916,16.8264084 C7.57463481,15.3079672 4.69203285,12.4253652 3.17359164,8.82640836 L5.29408795,6.70591205 C5.68612671,6.31387329 6,5.55641359 6,5.00922203 L6,0.990777969 C6,0.45097518 5.55237094,3.33066907e-16 5.00019251,3.33066907e-16 L1.65110039,3.33066907e-16 L1.00214643,8.96910337e-16 C0.448676237,1.13735153e-15 -1.05725384e-09,0.445916468 -7.33736e-10,1.00108627 C-7.33736e-10,1.00108627 -3.44283713e-14,1.97634814 -3.44283713e-14,3 C-3.44283713e-14,12.3888407 7.61115925,20 17,20 C18.0236519,20 18.9989137,20 18.9989137,20 C19.5517984,20 20,19.5565264 20,18.9978536 L20,18.3488996 L20,14.9998075 C20,14.4476291 19.5490248,14 19.009222,14 L14.990778,14 C14.4435864,14 13.6861267,14.3138733 13.2940879,14.7059121 L11.1735916,16.8264084 Z" />
                                </svg>
                            </a>
                            <a href="#" class="block rounded-full hover:bg-gray-700 bg-gray-800 w-10 h-10 p-2 ml-4">
                                <svg viewBox="0 0 20 20" class="w-full h-full fill-current text-blue-500">
                                    <path
                                        d="M0,3.99406028 C0,2.8927712 0.894513756,2 1.99406028,2 L14.0059397,2 C15.1072288,2 16,2.89451376 16,3.99406028 L16,16.0059397 C16,17.1072288 15.1054862,18 14.0059397,18 L1.99406028,18 C0.892771196,18 0,17.1054862 0,16.0059397 L0,3.99406028 Z M8,14 C10.209139,14 12,12.209139 12,10 C12,7.790861 10.209139,6 8,6 C5.790861,6 4,7.790861 4,10 C4,12.209139 5.790861,14 8,14 Z M8,12 C9.1045695,12 10,11.1045695 10,10 C10,8.8954305 9.1045695,8 8,8 C6.8954305,8 6,8.8954305 6,10 C6,11.1045695 6.8954305,12 8,12 Z M16,7 L20,3 L20,17 L16,13 L16,7 Z" />
                                </svg>
                            </a>
                            <a href="#" class="block rounded-full hover:bg-gray-700 bg-gray-800 w-10 h-10 p-2 ml-4">
                                <svg viewBox="0 0 20 20" class="w-full h-full fill-current text-blue-500">
                                    <path
                                        d="M2.92893219,17.0710678 C6.83417511,20.9763107 13.1658249,20.9763107 17.0710678,17.0710678 C20.9763107,13.1658249 20.9763107,6.83417511 17.0710678,2.92893219 C13.1658249,-0.976310729 6.83417511,-0.976310729 2.92893219,2.92893219 C-0.976310729,6.83417511 -0.976310729,13.1658249 2.92893219,17.0710678 Z M9,11 L9,10.5 L9,9 L11,9 L11,15 L9,15 L9,11 Z M9,5 L11,5 L11,7 L9,7 L9,5 Z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <!-- Chat Section -->
                    <div class="chat-body p-4 flex-1 overflow-y-scroll" ref="messagesContainer">
                        <div class="mb-20">
                            <div
                                class="max-w-2xl mx-4 sm:max-w-sm md:max-w-sm lg:max-w-sm xl:max-w-sm sm:mx-auto md:mx-auto lg:mx-auto xl:mx-auto mt-16 rounded-lg text-gray-900">
                                <div class="rounded-t-lg h-32 overflow-hidden">
                                    <!-- <img class="object-cover object-top w-full"
                                        src='https://images.unsplash.com/photo-1549880338-65ddcdfd017b?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ'
                                        alt='Mountain'> -->
                                </div>
                                <div class="mx-auto w-32 h-32 relative -mt-16 border-4 rounded-full overflow-hidden"
                                    :class="isActive[0]
                                        ? 'border-green-400'
                                        : 'border-gray-300'
                                        ">
                                    <img class="object-cover object-center h-32" :src="'http://172.16.161.34:8080/hrms' +
                                        usersPhoto
                                        " alt="Woman looking front" />
                                </div>
                                <div class="text-center mt-2">
                                    <h2 class="font-semibold text-white">
                                        {{ usersName }}
                                    </h2>
                                    <p class="text-gray-500">
                                        {{
                                            isActive[0]?.time
                                                ? "Active Now"
                                                : "Active " +
                                                dayjs(isOffline).toNow(true) +
                                                "ago"
                                        }}
                                    </p>
                                </div>
                                <!-- <ul class="py-4 mt-2 text-gray-700 flex items-center justify-around">
                                    <li class="flex flex-col items-center justify-around">
                                        <svg class="w-4 fill-current text-white" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <div>2k</div>
                                    </li>
                                    <li class="flex flex-col items-center justify-between">
                                        <svg class="w-4 fill-current text-white" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M7 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1c2.15 0 4.2.4 6.1 1.09L12 16h-1.25L10 20H4l-.75-4H2L.9 10.09A17.93 17.93 0 0 1 7 9zm8.31.17c1.32.18 2.59.48 3.8.92L18 16h-1.25L16 20h-3.96l.37-2h1.25l1.65-8.83zM13 0a4 4 0 1 1-1.33 7.76 5.96 5.96 0 0 0 0-7.52C12.1.1 12.53 0 13 0z" />
                                        </svg>
                                        <div>10k</div>
                                    </li>
                                    <li class="flex flex-col items-center justify-around">
                                        <svg class="w-4 fill-current text-white" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9 12H1v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-6h-8v2H9v-2zm0-1H0V5c0-1.1.9-2 2-2h4V2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1h4a2 2 0 0 1 2 2v6h-9V9H9v2zm3-8V2H8v1h4z" />
                                        </svg>
                                        <div>15</div>
                                    </li>
                                </ul> -->
                            </div>
                            <div class="p-4 mx-8 mt-2">
                                <div class="flex items-center">
                                    <div class="flex-grow border-t border-gray-700"></div>
                                    <span class="mx-3 text-gray-400 font-semibold text-sm uppercase">Start
                                        Message</span>
                                    <div class="flex-grow border-t border-gray-700"></div>
                                </div>
                            </div>
                        </div>
                        <div v-for="(item, index) in messages" :key="item.id">
                            <div class="flex flex-row" :class="item.sender_id == page.auth.user.id
                                ? 'justify-end'
                                : 'justify-start'
                                ">
                                <div class="w-7 h-7 border-none relative flex flex-shrink-0 mr-1"
                                    v-if="item.attachment == ''">
                                    <img v-if="
                                        item.sender_id != page.auth.user.id
                                    " :class="!messages[index - 1] ||
                                        messages[index - 1].sender_id !==
                                        item.sender_id
                                        ? 'shadow-md rounded-full w-full h-full object-cover'
                                        : ''
                                        " :src="!messages[index - 1] ||
                                                messages[index - 1].sender_id !==
                                                item.sender_id
                                                ? 'http://172.16.161.34:8080/hrms' +
                                                usersPhoto
                                                : ''
                                                " alt="" />
                                </div>
                                <div class="messages text-sm text-gray-700 grid grid-flow-row gap-2">
                                    <div class="flex items-end group mb-2" :class="item.sender_id == page.auth.user.id
                                        ? 'flex-row-reverse'
                                        : ''
                                        ">
                                        <Transition enter-active-class="transition ease-out duration-500"
                                            enter-from-class="-translate-y-5 opacity-0"
                                            enter-to-class="translate-y-0 opacity-100"
                                            leave-active-class="transition ease-in duration-200"
                                            leave-from-class="translate-y-0 opacity-100"
                                            leave-to-class="-translate-y-5 opacity-0">
                                            <div v-if="
                                                item.read === 1 &&
                                                item.sender_id ==
                                                page.auth.user.id &&
                                                index === lastReadIndex
                                            " class="w-3 h-3 flex-shrink-0">
                                                <img v-if="
                                                    item.sender_id ==
                                                    page.auth.user.id
                                                " class="rounded-full w-full h-full object-cover" alt="user avatar"
                                                    :src="'http://172.16.161.34:8080/hrms' +
                                                        usersPhoto
                                                        " />
                                            </div>
                                        </Transition>
                                        <!-- Message bubble -->
                                        <div class="mx-2 max-w-xs lg:max-w-md" :class="item.sender_id ==
                                            page.auth.user.id
                                            ? 'self-end'
                                            : 'self-start'
                                            ">
                                            <!-- Reply Section -->
                                            <div v-if="item.toReply"
                                                class="px-4 py-2 -mb-2 text-sm max-w-xs md:max-w-sm lg:max-w-md truncate"
                                                :class="item.sender_id ==
                                                    page.auth.user.id
                                                    ? 'bg-blue-950 text-gray-400 border-l-4 w-fit ml-auto border-blue-950 mr-2 rounded-l-[2rem] rounded-b-[2rem]'
                                                    : 'bg-gray-600 text-gray-400 border-l-4 border-gray-600 ml-2 rounded-r-[2rem] rounded-b-[2rem]'
                                                    ">
                                                <p class="truncate h-6 leading-6">
                                                    {{ item.toReply }}
                                                </p> 
                                            </div>
                                            <!-- {{ item }} -->

                                            <!-- Actual Message -->
                                            <p v-if="!item.attachment" class="px-6 py-3 text-gray-200" :class="[
                                                // background
                                                item.sender_id ==
                                                    page.auth.user.id
                                                    ? 'bg-blue-800 w-fit ml-auto'
                                                    : 'bg-gray-800 w-fit mr-auto',
                                                // bubble shaping
                                                isFirstInGroup(index) &&
                                                    item.sender_id ==
                                                    page.auth.user.id
                                                    ? 'rounded-t-[2rem] rounded-l-[2rem]'
                                                    : isFirstInGroup(index)
                                                        ? 'rounded-t-[2rem] rounded-r-[2rem]'
                                                        : '',
                                                isLastInGroup(index) &&
                                                    item.sender_id ==
                                                    page.auth.user.id
                                                    ? 'rounded-b-[2rem] rounded-l-[2rem]'
                                                    : isLastInGroup(index)
                                                        ? 'rounded-b-[2rem] rounded-r-[2rem]'
                                                        : '',
                                                isMiddleInGroup(index) &&
                                                    item.sender_id ==
                                                    page.auth.user.id
                                                    ? 'rounded-l-[2rem]'
                                                    : isMiddleInGroup(index)
                                                        ? 'rounded-r-[2rem]'
                                                        : '',
                                            ]">
                                                {{ item.message }}
                                            </p>
                                            <p v-else>
                                                <!-- kanindg -->

                                            <div
                                                class="px-6 py-3 block w-64 h-64 relative flex flex-shrink-0 max-w-xs lg:max-w-md mt-2">

                                                <a-image class="w-64 h-64" style="height: 100%;" :class="item.sender_id ==
                                                    page.auth.user.id ? 'rounded-l-[2rem]' : 'rounded-r-[2rem]'"
                                                    :src="'/storage/uploads/' + item.attachment" alt="hiking" />
                                            </div>
                                            </p>
                                            <Transition enter-active-class="transition duration-300 ease-out"
                                                enter-from-class="opacity-0 scale-50"
                                                enter-to-class="opacity-100 scale-100"
                                                leave-active-class="transition duration-200 ease-in"
                                                leave-from-class="opacity-100 scale-100"
                                                leave-to-class="opacity-0 scale-50">
                                                <div v-if="
                                                    item.react &&
                                                    item.react != '0'
                                                " class="flex relative z-[99]" :class="item.sender_id ==
                                                    page.auth.user.id
                                                    ? 'justify-end'
                                                    : 'justify-start'
                                                    ">
                                                    <p
                                                        class="-mt-1.5 mr-3 ml-3 border rounded-full bg-gray-500 px-1 py-1 animate-pop">
                                                        {{ item.react }}
                                                    </p>
                                                </div>
                                            </Transition>
                                        </div>
                                        <!-- Action buttons -->
                                        <div class="hidden group-hover:flex" :class="item.sender_id ==
                                            page.auth.user.id
                                            ? 'flex-row-reverse '
                                            : ''
                                            ">
                                            <button type="button" :class="item.react != '0'
                                                ? 'mb-7'
                                                : ''
                                                "
                                                class="flex mb-1.5 ml-1 flex-shrink-0 focus:outline-none rounded-full text-gray-500 hover:text-red-500 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                                <!-- Trash Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="currentColor" class="w-full h-full">
                                                    <path fill-rule="evenodd"
                                                        d="M9 3a1 1 0 00-1 1v1H4.5a.75.75 0 000 1.5H5v12.25A2.75 2.75 0 007.75 21.5h8.5A2.75 2.75 0 0019 18.75V6.5h.5a.75.75 0 000-1.5H16V4a1 1 0 00-1-1H9zm2.25 5.25a.75.75 0 00-1.5 0v8.5a.75.75 0 001.5 0v-8.5zm4 0a.75.75 0 00-1.5 0v8.5a.75.75 0 001.5 0v-8.5z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <button @click="reply(item)" type="button"
                                                class="flex flex-shrink-0 ml-1 focus:outline-none rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                                <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                                    <path
                                                        d="M19,16.685c0,0-2.225-9.732-11-9.732V2.969L1,9.542l7,6.69v-4.357C12.763,11.874,16.516,12.296,19,16.685z" />
                                                </svg>
                                            </button>
                                            <button type="button" @click="reactOpen(item)"
                                                class="flex flex-shrink-0 ml-1 focus:outline-none rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                                <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                                    <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-3.54-4.46a1 1 0 0 1 1.42-1.42
            3 3 0 0 0 4.24 0 1 1 0 0 1 1.42 1.42 5 5 0 0 1-7.08 0zM9 11a1 1 0 1 1 0-2
            1 1 0 0 1 0 2zm6 0a1 1 0 1 1 0-2
            1 1 0 0 1 0 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <p class="p-4 text-center text-sm text-gray-500">
                                FRI 3:04 PM
                            </p> -->

                            <div v-if="
                                typingIndicator &&
                                index === messages.length - 1 &&
                                (repId == item.recipient_id ||
                                    repId == item.sender_id)
                            ">
                                <div class="flex flex-row">
                                    <div class="w-8 h-8 relative flex flex-shrink-0 mr-4 justify-start">
                                        <img class="shadow-md rounded-full w-full h-full object-cover" :src="'http://172.16.161.34:8080/hrms' +
                                            usersPhoto
                                            " alt="" />
                                    </div>

                                    <!-- Messages -->
                                    <div class="messages text-sm text-gray-700 grid grid-flow-row gap-2">
                                        <div class="flex items-center group mb-2">
                                            <!-- Bubble -->
                                            <div
                                                class="px-6 py-3 max-w-xs lg:max-w-md text-gray-200 rounded-b-[2rem] rounded-r-[2rem] bg-gray-800 relative">
                                                <!-- Typing dots -->
                                                <div class="flex items-end space-x-1">
                                                    <span
                                                        class="w-2 h-2 rounded-full bg-gray-300 animate-jump delay-0"></span>
                                                    <span
                                                        class="w-2 h-2 rounded-full bg-gray-300 animate-jump delay-200"></span>
                                                    <span
                                                        class="w-2 h-2 rounded-full bg-gray-300 animate-jump delay-400"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div></div>
                    </div>

                    <div v-if="showPickerReact" class="picker-wrapper-react">
                        <emoji-picker @emoji-click="emojiReact" class="emoji-picker"></emoji-picker>
                    </div>

                    <div class="chat-footer flex-none">
                        <div class="ml-12 flex">
                            <div v-if="toReply" class="flex items-center gap-2 ml-12 mt-2 -mb-3.5">
                                <!-- Text bubble -->
                                <div class="px-3 py-2 rounded-full bg-gray-800 text-left max-w-md break-words truncate">
                                    {{ toReply }}
                                </div>

                                <!-- Button -->
                                <button type="button" @click="clearToReply"
                                    class="flex-shrink-0 focus:outline-none text-gray-500 hover:text-red-700 w-6 h-6">
                                    <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-row items-center p-4">

                            <a-upload v-model:file-list="fileList" :show-preview-icon="false" :show-upload-list="false"
                                :multiple="false" :before-upload="handleBeforeUpload"
                                accept="image/png,image/jpeg,image/jpg">
                                <button type="button"
                                    class="flex flex-shrink-0 focus:outline-none mx-2 block text-blue-600 hover:text-blue-700 w-8 h-8">
                                    <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                        <path
                                            d="M10,1.6c-4.639,0-8.4,3.761-8.4,8.4s3.761,8.4,8.4,8.4s8.4-3.761,8.4-8.4S14.639,1.6,10,1.6z M15,11h-4v4H9  v-4H5V9h4V5h2v4h4V11z" />
                                    </svg>
                                </button>
                            </a-upload>

                            <div class="relative flex-grow" ref="containerRef">
                                <label>
                                    <input @keyup.enter="sendMessage()" @input="sendTyping" id="message-input"
                                        @blur="sendTyping(false)" @mouseleave="sendTyping(false)"
                                        class="rounded-full py-2 pl-3 pr-10 w-full border border-gray-800 focus:border-gray-700 bg-gray-800 focus:bg-gray-900 focus:outline-none text-gray-200 focus:shadow-md transition duration-300 ease-in"
                                        type="text" v-model="form.message" placeholder="Aa.." />
                                    <button type="button" @click="showPicker = !showPicker"
                                        class="absolute top-0 right-0 mt-2 mr-3 flex flex-shrink-0 focus:outline-none block text-blue-600 hover:text-blue-700 w-6 h-6">
                                        <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                            <path
                                                d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM6.5 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm7 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm2.16 3a6 6 0 0 1-11.32 0h11.32z" />
                                        </svg>
                                    </button>
                                </label>

                                <div v-if="showPicker" class="picker-wrapper">
                                    <emoji-picker @emoji-click="onEmojiClick" class="emoji-picker"></emoji-picker>
                                </div>
                            </div>
                            <button type="button" @click="sendLike"
                                class="flex flex-shrink-0 focus:outline-none mx-2 block text-blue-600 hover:text-blue-700 w-6 h-6">
                                <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                    <path
                                        d="M11.0010436,0 C9.89589787,0 9.00000024,0.886706352 9.0000002,1.99810135 L9,8 L1.9973917,8 C0.894262725,8 0,8.88772964 0,10 L0,12 L2.29663334,18.1243554 C2.68509206,19.1602453 3.90195042,20 5.00853025,20 L12.9914698,20 C14.1007504,20 15,19.1125667 15,18.000385 L15,10 L12,3 L12,0 L11.0010436,0 L11.0010436,0 Z M17,10 L20,10 L20,20 L17,20 L17,10 L17,10 Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col flex-auto border-l border-gray-800" v-if="addChat">
                    <AddChat @messageEmit="getMessageEmit"></AddChat>
                </div>
                <div class="flex flex-col flex-auto border-l border-gray-800" v-else-if="!repId && !addChat">
                    <div class="flex justify-center h-[100%] items-center">
                        <div class="max-w-xl w-full text-center bg-gradient-to-b p-8 md:p-12 backdrop-blur-sm">
                            <!-- Illustration -->
                            <img style="height: 400px" src="/images/emptymessage.svg" alt="" />
                            <div
                                class="mx-auto mb-6 w-28 h-28 rounded-full bg-gradient-to-tr from-indigo-600 to-violet-500/90 flex items-center justify-center shadow-lg transform-gpu">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 12.5a8.5 8.5 0 11-17 0 8.5 8.5 0 0117 0z" opacity="0.12" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 10h.01M12 10h.01M16 10h.01M8 14h8" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 8v6a5 5 0 0010 0V8" />
                                </svg>
                            </div>

                            <!-- Title + Description -->
                            <h3 class="text-2xl md:text-3xl font-semibold text-white mb-2">
                                No messages yet
                            </h3>
                            <p class="text-sm md:text-base text-gray-300 max-w-[38ch] mx-auto mb-6">
                                Looks like your inbox is clear — enjoy the calm.
                                When someone sends you a message it’ll show up
                                here instantly.
                            </p>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">

                            </div>

                            <!-- Subtle hint -->
                            <p class="mt-4 text-xs text-gray-500">
                                Tip: Try composing a message or invite a friend
                                to start a conversation.
                            </p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useOnlineUsersStore } from "@/stores/online-store";
import { router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { UploadProps } from "ant-design-vue";
import axios from "axios";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "emoji-picker-element";

const fileList = ref([]);

import {
    ref,
    onMounted,
    reactive,
    nextTick,
    watch,
    computed,
    onUnmounted,
    watchEffect,
} from "vue";
import AddChat from "./AddChat.vue";

dayjs.extend(relativeTime);

interface UserOnline {
    id: number;
    name: string;
    photo: string;
}

// const props = defineProps<{
//     onlineUsers: any;
// }>();

const page = usePage<{
    auth: {
        user: {
            id: number;
            photo: string;
        };
    };
}>().props;

interface User {
    id: number;
    firstname: string;
}

interface EveryMessage {
    user_id: number;
    message: string;
    firstname: string;
    lastname: string;
    created_at: string;
    latest_at: string;
    count: number;
    countUnread: number;
    photo: string;
    isOnline: boolean;
    photoSeen: string;
}

interface Messages {
    recipient_id: number;
    sender_id: number;
    message: string;
    attachment: string;
    id: number;
    read: number;
    toReply: string;
    react: string;
}

const messageUsers = ref<User[]>([]);
const everyMessage = ref<EveryMessage[]>([]);
const messages = ref<Messages[]>([]);
const repId = ref<number>();
const messagesContainer = ref<HTMLDivElement | null>(null);
const usersName = ref<string>();
const usersPhoto = ref<string>();
const isOffline = ref<string>();
const addChat = ref<boolean>(false);

const isActive = computed(() => {
    const onlineIds = getOnlineUsers.value.filter(
        (user: any) => user.id == repId.value
    );
    return onlineIds;
});

const form = reactive({
    message: "" as string,
    react: "" as string,
    attachment: fileList.value
});

const showPickerReact = ref(false);
const showPicker = ref(false);
const containerRef = ref(null);
const idMessage = ref(null);

const togglePicker = () => {
    showPicker.value = !showPicker.value;
};

const onEmojiClick = (event) => {
    form.message += event.detail.unicode;
    showPicker.value = false;
};
const sendLike = () => {
    form.message = "👍";

    sendMessage();
};
const emojiReact = async (event) => {
    form.react += event.detail.unicode;
    showPickerReact.value = false;

    const { data } = await axios.put(route("message.react.message"), {
        id: idMessage.value,
        react: form.react,
        rep: repId.value,
    });
    const index = messages.value.findIndex((m) => m.id == data.message?.id);

    if (index !== -1) {
        // Merge updates instead of replacing the whole object
        messages.value[index] = data?.message;
    } else {
        // Add new if not found
        messages.value.push(data.message);
    }

    form.react = "";
};

const reactOpen = (index) => {
    showPickerReact.value = !showPickerReact.value;
    idMessage.value = index.id;
};

const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        showPicker.value = false;
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop =
                messagesContainer.value.scrollHeight;
        }
    });
};

const getEveryMessage = async () => {
    const { data } = await axios.get(route("message.get.every.message"));
    everyMessage.value = data.users;

    const onlineIds = getOnlineUsers.value.map((user) => user.id);

    everyMessage.value = data.users.map((user: any) => {
        return {
            ...user,
            isOnline: onlineIds.includes(user.user_id), // ✅ mark if online
        };
    });
};

const getMesssage = async (id: number) => {
    const { data } = await axios.get(route("message.get.message"), {
        params: {
            id: id,
        },
    });
    // alert(1);
    isOffline.value = data.isOffline;
    usersName.value = data.name;
    usersPhoto.value = data.photo;
    messages.value = data.messages;
    repId.value = data.rep_id;
    scrollToBottom();
    seenMessage();
    getEveryMessage();
    // getMesssage(repId.value)
};

const lastReadIndex = computed(() => {
    return messages.value
        .map((m, i) => ({ ...m, i }))
        .filter((m) => m.read === 1)
        .map((m) => m.i)
        .pop(); // last index where read == 1 and sender is the user
});

const echoMessage = async () => {
    await window.Echo.private(`message.${page.auth.user.id}`)
        .listen(".message-event", (e) => {
            if (
                repId.value == e.message.sender_id ||
                repId.value == e.message.recipient_id
            ) {
                const index = messages.value.findIndex(
                    (m) => m.id == e.message?.id
                );

                if (index !== -1) {
                    messages.value[index] = e?.message;
                } else {
                    scrollToBottom();
                    // Add new if not found
                    messages.value.push(e.message);
                }
                // messages.value.push(e.message);

                seenMessage();
            }
            getEveryMessage();
        })
        .listenForWhisper("typings", (e) => {
            typingIndicator.value = e.typing;
            scrollToBottom();
        });
};
const echoMessageSeen = async () => {
    await window.Echo.private(`message-seen.${page.auth.user.id}`).listen(
        ".message-seen-event",
        (e) => {
            if (repId.value == e.message?.recipient_id) {
                const index = messages.value.findIndex(
                    (m) => m.id === e.message?.id
                );

                if (index !== -1) {
                    // Replace reactively
                    messages.value.splice(index, 1, e.message);
                } else {
                    // If not found, add it
                    messages.value.push(e.message);
                }
            }
        }
    );
};

const isFirstInGroup = (index: number) => {
    if (index === 0) return true;
    return (
        messages.value[index].sender_id !== messages.value[index - 1].sender_id
    );
};

const isLastInGroup = (index: number) => {
    if (index === messages.value.length - 1) return true;
    return (
        messages.value[index].sender_id !== messages.value[index + 1].sender_id
    );
};

const isMiddleInGroup = (index: number) => {
    return !isFirstInGroup(index) && !isLastInGroup(index);
};

const typingIndicator = ref(false);

const sendTyping = (isTyping) => {
    window.Echo.private(`message.${repId.value}`).whisper("typings", {
        typing: isTyping,
    });
    scrollToBottom();
};

const input = document.getElementById("message-input");

// Focused / hovered → true
input?.addEventListener("focus", () => sendTyping(true));

// Lost focus / mouse leaves → false
input?.addEventListener("blur", () => sendTyping(false));
input?.addEventListener("mouseleave", () => sendTyping(false));

const toReply = ref("");
const toReplyId = ref(null);

const reply = (item) => {
    toReply.value = item.message;
    toReplyId.value = item.id;
};

const sendMessage = async () => {
    const { data } = await axios.post(route("message.send.message"), {
        id: repId.value,
        message: form.message,
        replyId: toReplyId.value,
    });
    form.message = "";
    toReply.value = null;
    toReplyId.value = null;

    messages.value.push(data.message);

    scrollToBottom();
    getEveryMessage();
    getMesssage(repId.value);
    sendTyping(false);
    // seenMessage();
};

const clearToReply = () => {
    toReply.value = null;
    toReplyId.value = null;
};

const unexpandMessage = async () => {
    await router.get(route("dashboard"), {
        preserveState: true,
        replace: true,
    });
};

const seenMessage = async () => {
    const { data } = await axios.put(route("message.seen.message"), {
        id: repId.value,
    });

    if (data.message[0]?.recipient_id === page.auth.user.id) {
        const index = messages.value.findIndex(
            (m) => m.id == data.message[0]?.id
        );

        if (index !== -1) {
            // Merge updates instead of replacing the whole object
            messages.value[index] = data?.message[0];
        } else {
            // Add new if not found
            messages.value.push(data.message);
        }
        getEveryMessage();
    }
};
const getMesssageEcho = async () => {
    await window.Echo.private(`get-message.${page.auth.user.id}`).listen(
        ".get-message-event",
        (e) => {
            everyMessage.value = e.message;
        }
    );
};

const onlineUsersStore = useOnlineUsersStore();

const { setOnlineUsers, addOnlineUser, removeOnlineUser } = onlineUsersStore;

const searchUsers = ref();

const getOnlineUsers = computed(() => onlineUsersStore.onlineUsers);
const handleBeforeUpload = async (file: File) => {
    // Check type
    const isImage = file.type.startsWith("image/");
    const isSvg = file.type === "image/svg+xml";
    if (!isImage || isSvg) {
        alert("Only PNG, JPG, and GIF images are allowed. SVG is not allowed.");
        return false;
    }

    // Check size

    await uploadAttachment(file);
    return false;
};

const uploadAttachment = async (file: File | any) => {
    const formData = new FormData();

    formData.append("id", String(repId.value));
    formData.append("message", form.message ?? "");
    formData.append("replyId", toReplyId.value ?? "");

    // normalize file (support both direct File and AntD file object)
    const realFile = file.originFileObj ?? file;
    formData.append("attachment", realFile);

    try {
        const { data } = await axios.post(route("message.send.message"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        // reset
        form.message = "";
        toReply.value = null;
        toReplyId.value = null;
        form.attachment = [];

        messages.value.push(data.message);

        scrollToBottom();
        getEveryMessage();
        getMesssage(repId.value);
        sendTyping(false);
    } catch (error) {
        console.error("Upload failed:", error);
    }

    return false; // important when using beforeUpload
};

const addNewChat = async () => {
    // alert();
    addChat.value = true;
}

const getMessageEmit = (id) => {
    getMesssage(id);
    addChat.value = false;
}

onMounted(() => {
    if (page.auth) {
        window.Echo.join("online.users")
            .here((users) => setOnlineUsers(users))
            .joining(async (user) => addOnlineUser(user))
            .leaving(async (user) => removeOnlineUser(user));
    }
    document.addEventListener("click", handleClickOutside);
    getEveryMessage();
    echoMessage();
    getMesssageEcho();
    echoMessageSeen();
});

onUnmounted(() => {
    if (page.auth) {
        window.Echo.leaveAllChannels();
    }
    document.removeEventListener("click", handleClickOutside);
});

watch(
    searchUsers,
    async (newVal) => {
        if (newVal) {
            try {
                const { data } = await axios.get(
                    route("message.get.every.message"),
                    {
                        params: {
                            search: newVal,
                        },
                    }
                );
                everyMessage.value = data.users; // make sure backend response key is correct
            } catch (error) {
                console.error("Error fetching messages:", error);
            }
        }
    },
    { deep: true }
);
</script>

<style scoped>
/* can be configured in tailwind.config.js */
.group:hover .group-hover\:block {
    display: block;
}

.hover\:w-64:hover {
    width: 45%;
}

/* NO NEED THIS CSS - just for custom scrollbar which can also be configured in tailwind.config.js*/
::-webkit-scrollbar {
    width: 2px;
    height: 2px;
}

::-webkit-scrollbar-button {
    width: 0px;
    height: 0px;
}

::-webkit-scrollbar-thumb {
    background: #2d3748;
    border: 0px none #ffffff;
    border-radius: 50px;
}

::-webkit-scrollbar-thumb:hover {
    background: #2b6cb0;
}

::-webkit-scrollbar-thumb:active {
    background: #000000;
}

::-webkit-scrollbar-track {
    background: #1a202c;
    border: 0px none #ffffff;
    border-radius: 50px;
}

::-webkit-scrollbar-track:hover {
    background: #666666;
}

::-webkit-scrollbar-track:active {
    background: #333333;
}

::-webkit-scrollbar-corner {
    background: transparent;
}

@keyframes jump {

    0%,
    100% {
        transform: translateY(0);
        opacity: 0.9;
    }

    50% {
        transform: translateY(-6px);
        opacity: 1;
    }
}

/* generic animation utility */
.animate-jump {
    animation: jump 0.8s ease-in-out infinite;
}

/* simple delay helpers (Tailwind won't generate these unless configured, so we add them here) */
.delay-0 {
    animation-delay: 0s;
}

.delay-200 {
    animation-delay: 0.12s;
}

/* slight stagger */
.delay-400 {
    animation-delay: 0.24s;
}

.emoji-picker-container {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 500px;
    margin: 20px;
}

.emoji-button {
    width: 40px;
    height: 40px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.2s ease;
}

.emoji-button:hover {
    background: #f5f5f5;
    transform: scale(1.05);
}

.picker-wrapper {
    position: absolute;
    bottom: 100%;
    left: 0;
    z-index: 1000;
    margin-bottom: 10px;
}

.picker-wrapper-react {
    position: absolute;
    bottom: 10%;
    left: 50%;
    z-index: 1000;
    margin-bottom: 10px;
}

.emoji-picker {
    --num-columns: 8;
    --emoji-size: 1.5rem;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.message-input {
    min-height: 100px;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    resize: vertical;
    transition: border-color 0.2s ease;
}

.message-input:focus {
    outline: none;
    border-color: #4285f4;
    box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.2);
}

@keyframes pop {
    0% {
        transform: scale(0.5);
        opacity: 0;
    }

    60% {
        transform: scale(1.2);
        opacity: 1;
    }

    100% {
        transform: scale(1);
    }
}

.animate-pop {
    animation: pop 0.3s ease-out;
}
</style>
