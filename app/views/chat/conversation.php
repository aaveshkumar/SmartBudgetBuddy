<?php
require __DIR__ . '/../common/header.php';
?>

<style>
.chat-container {
    display: flex;
    height: calc(100vh - 200px);
    min-height: 500px;
}
.chat-sidebar {
    width: 300px;
    border-right: 1px solid #dee2e6;
    overflow-y: auto;
}
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.chat-header {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
}
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f0f2f5;
}
.chat-input {
    padding: 15px;
    border-top: 1px solid #dee2e6;
    background: white;
}
.message {
    max-width: 70%;
    margin-bottom: 10px;
    padding: 10px 15px;
    border-radius: 15px;
    position: relative;
}
.message-sent {
    margin-left: auto;
    background: #0d6efd;
    color: white;
    border-bottom-right-radius: 5px;
}
.message-received {
    background: white;
    border-bottom-left-radius: 5px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
.message-time {
    font-size: 0.7rem;
    opacity: 0.7;
    margin-top: 5px;
}
.message-sent .message-time {
    text-align: right;
}
@media (max-width: 768px) {
    .chat-sidebar {
        display: none;
    }
    .chat-container {
        height: calc(100vh - 150px);
    }
}
</style>

<div class="container-fluid px-0">
    <div class="chat-container">
        <div class="chat-sidebar d-none d-md-block">
            <div class="p-3 border-bottom">
                <h6 class="mb-0"><i class="fas fa-comments"></i> Conversations</h6>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($conversations as $conv): ?>
                    <?php $isActive = $conv['id'] == $conversation['id']; ?>
                    <a href="/chat/<?= $conv['id'] ?>" class="list-group-item list-group-item-action <?= $isActive ? 'active' : '' ?>">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <div class="<?= $isActive ? 'bg-white text-primary' : 'bg-primary text-white' ?> rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="fas fa-user" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold text-truncate" style="font-size: 0.9rem;">
                                    <?= htmlspecialchars($conv['other_party_name']) ?>
                                </div>
                                <small class="text-truncate d-block <?= $isActive ? 'text-white-50' : 'text-muted' ?>">
                                    <?= htmlspecialchars($conv['job_title']) ?>
                                </small>
                            </div>
                            <?php if (($conv['unread_count'] ?? 0) > 0 && !$isActive): ?>
                                <span class="badge bg-danger"><?= $conv['unread_count'] ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="chat-main">
            <div class="chat-header">
                <div class="d-flex align-items-center">
                    <a href="/chat" class="btn btn-link text-dark d-md-none me-2">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h6 class="mb-0"><?= htmlspecialchars($otherPartyName) ?></h6>
                        <small class="text-muted">
                            <i class="fas fa-briefcase"></i> <?= htmlspecialchars($conversation['job_title']) ?>
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <?php if (empty($messages)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-comment-dots fa-3x mb-3"></i>
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <?php $isSent = $message['sender_id'] == $currentUser['id']; ?>
                        <div class="message <?= $isSent ? 'message-sent' : 'message-received' ?>" data-message-id="<?= $message['id'] ?>">
                            <div><?= nl2br(htmlspecialchars($message['message'])) ?></div>
                            <div class="message-time">
                                <?= date('M j, g:i A', strtotime($message['created_at'])) ?>
                                <?php if ($isSent && $message['is_read']): ?>
                                    <i class="fas fa-check-double ms-1"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="chat-input">
                <form id="messageForm" onsubmit="sendMessage(event)">
                    <div class="input-group">
                        <input type="text" class="form-control" id="messageInput" placeholder="Type your message..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var conversationId = <?= $conversation['id'] ?>;
var currentUserId = <?= $currentUser['id'] ?>;
var lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
var chatMessages = document.getElementById('chatMessages');
var messageInput = document.getElementById('messageInput');
var sendBtn = document.getElementById('sendBtn');

function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function formatTime(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' + 
           date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function addMessage(message, isSent) {
    var div = document.createElement('div');
    div.className = 'message ' + (isSent ? 'message-sent' : 'message-received');
    div.dataset.messageId = message.id;
    
    var content = document.createElement('div');
    content.innerHTML = message.message.replace(/\n/g, '<br>');
    div.appendChild(content);
    
    var time = document.createElement('div');
    time.className = 'message-time';
    time.textContent = formatTime(message.created_at);
    div.appendChild(time);
    
    // Remove empty state if exists
    var emptyState = chatMessages.querySelector('.text-center.text-muted');
    if (emptyState) emptyState.remove();
    
    chatMessages.appendChild(div);
    scrollToBottom();
}

function sendMessage(e) {
    e.preventDefault();
    
    var message = messageInput.value.trim();
    if (!message) return;
    
    sendBtn.disabled = true;
    messageInput.disabled = true;
    
    var formData = new FormData();
    formData.append('message', message);
    
    fetch('/chat/' + conversationId + '/send', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success && data.message) {
            addMessage(data.message, true);
            lastMessageId = data.message.id;
            messageInput.value = '';
        } else if (data.error) {
            alert(data.error);
        }
    })
    .catch(function(err) {
        console.error('Send error:', err);
    })
    .finally(function() {
        sendBtn.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
    });
}

function pollMessages() {
    fetch('/chat/' + conversationId + '/messages?last_id=' + lastMessageId)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(function(msg) {
                    if (!document.querySelector('[data-message-id="' + msg.id + '"]')) {
                        addMessage(msg, msg.sender_id == currentUserId);
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    }
                });
            }
        })
        .catch(function(err) {
            console.log('Poll error:', err);
        });
}

scrollToBottom();
setInterval(pollMessages, 3000);
messageInput.focus();
</script>

<?php require __DIR__ . '/../common/footer.php'; ?>
