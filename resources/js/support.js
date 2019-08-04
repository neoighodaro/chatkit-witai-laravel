import { ChatManager, TokenProvider } from '@pusher/chatkit-client';

window.PusherChatManager = new ChatManager({
    userId: 'admin',
    instanceLocator: process.env.MIX_CHATKIT_INSTANCE_LOCATOR,
    tokenProvider: new TokenProvider({ url: '/chatkit/authenticate' })
});

PusherChatManager.connect().then(currentUser => {
    let currentRoomId;

    // ----------------------------------------------------------------------
    // Add the list of rooms to the sidebar on the right of the dashboard
    // ----------------------------------------------------------------------

    for (let index = 0; index < currentUser.rooms.length; index++) {
        const room = currentUser.rooms[index];
        $('#rooms').append(
            `<li class="nav-item">
                <a data-room-id="${room.id}" class="nav-link" href="#">
                    ${room.name}
                </a>
            </li>`
        );
    }

    // ----------------------------------------------------------------------
    // On click of the chat room name, load the messages for the chatroom
    // ----------------------------------------------------------------------

    $('#rooms').on('click', 'li', ({ target }) => {
        const { roomId } = $(target).data();
        const roomName = $(target).text();

        const parseMessage = message => {
            let msg = '';
            for (let index = 0; index < message.parts.length; index++) {
                const part = message.parts[index];
                if (part.partType === 'inline') {
                    msg += part.payload.content;
                }
            }

            $('#chat-msgs').prepend(
                `<tr>
                <td>
                    <div class="sender">
                        ${message.senderId} @ <span class="date">${message.createdAt}</span>
                    </div>
                    <div class="message">${msg}</div>
                </td>
            </tr>`
            );
        };

        if (roomId) {
            $('#chat-msgs').html('') && $('.response').show();
            $('#room-title').text(`Room: ${roomName}`);

            currentRoomId = roomId;

            currentUser.subscribeToRoomMultipart({
                messageLimit: 100,
                roomId: `${roomId}`,
                hooks: { onMessage: parseMessage }
            });
        }
    });

    // ----------------------------------------------------------------------
    // When a message is being responded to, fire the event below
    // ----------------------------------------------------------------------

    $('#replyMessage').on('submit', evt => {
        evt.preventDefault();

        currentUser
            .sendSimpleMessage({
                roomId: `${currentRoomId}`,
                text: $('#replyMessage input').val()
            })
            .then(() => $('#replyMessage input').val(''));
    });
});
