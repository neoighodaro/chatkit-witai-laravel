require('./bootstrap');

import { ChatManager, TokenProvider } from '@pusher/chatkit-client';

(function() {
    let chat = {};
    let roomId;
    let currentUser;
    const chatPage = $(document);

    chatPage.ready(() => {
        const chatWindow = $('.chatbubble');
        const chatHeader = chatWindow.find('.unexpanded');
        const chatBody = chatWindow.find('.chat-window');

        // displays the appropriate chat window
        const showAppropriateChatWindow = () => {
            if (chat.name && chat.email) {
                return loadChatMessages();
            }

            chatBody.find('.chats').removeClass('active');
            chatBody.find('.login-screen').addClass('active');
        };

        // toggles the chat window display
        const toggleChatWindow = () => {
            chatWindow.toggleClass('opened');
            chatHeader.find('.title').text(chatWindow.hasClass('opened') ? 'Minimize' : 'Chat with Support');
        };

        // loads the chat messages to the chat window
        const loadChatMessages = () => {
            chatBody.find('.chats').addClass('active');
            chatBody.find('.login-screen').removeClass('active');

            // Connect
            window.PusherChatManager = new ChatManager({
                userId: chat.id,
                instanceLocator: process.env.MIX_CHATKIT_INSTANCE_LOCATOR,
                tokenProvider: new TokenProvider({ url: '/chatkit/authenticate' })
            });

            PusherChatManager.connect().then(user => {
                currentUser = user;

                chatBody.find('.loader-wrapper').hide();
                chatBody.find('.input, .messages').show();

                currentUser.subscribeToRoomMultipart({
                    messageLimit: 100,
                    roomId: (roomId = currentUser.rooms[0].id),
                    hooks: { onMessage: message => parseMessage(message) }
                });
            });
        };

        // logs into a chatkit session
        const logIntoChatSession = evt => {
            const name = $('#fullname').val();
            const email = $('#email').val();

            evt.preventDefault();
            chatBody.find('#loginScreenForm input, #loginScreenForm button').attr('disabled', true);

            axios.post('/chatkit/new', { name, email }).then(res => {
                chat = { id: res.data.id, name, email };
                showAppropriateChatWindow();
            });
        };

        // parse a single chat message from chatkit and adds it to the UI
        const parseMessage = message => {
            let msg = '';
            for (let index = 0; index < message.parts.length; index++) {
                const part = message.parts[index];
                if (part.partType === 'inline') {
                    msg += part.payload.content;
                }
            }

            chatBody.find('ul.messages').append(
                `<li class="clearfix message ${message.senderId === 'admin' ? 'support' : 'user'}">
                    <div class="sender">${message.senderId}</div>
                    <div class="message">${msg}</div>
                </li>`
            );

            chatBody.scrollTop(chatBody[0].scrollHeight);
        };

        // sends a message to the server which processes it and sends it to chatkit
        const sendMessageToSupport = evt => {
            evt.preventDefault();

            let params = { text: $('#newMessage').val(), roomId, userId: chat.id };

            axios.post('/chatkit/message', params).then(res => {
                $('#newMessage').val('');
            });
        };

        // Start
        showAppropriateChatWindow();
        chatHeader.on('click', toggleChatWindow);
        chatBody.find('#loginScreenForm').on('submit', logIntoChatSession);
        chatBody.find('#messageSupport').on('submit', sendMessageToSupport);
    });
})();
