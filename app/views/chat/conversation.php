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
.chat-input .input-group {
    align-items: flex-end;
}
.chat-input .input-group textarea {
    max-height: 150px;
    min-height: 38px;
}
.chat-input .input-group .btn {
    height: 38px;
    flex-shrink: 0;
}
.chat-input .btn .spinner-border {
    width: 1rem;
    height: 1rem;
    border-width: 2px;
}
.message-pending {
    opacity: 0.7;
}
.message-error {
    background: #f8d7da !important;
    color: #842029 !important;
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
                    <div class="text-center text-muted py-5" id="emptyState">
                        <i class="fas fa-comment-dots fa-3x mb-3"></i>
                        <?php if ($currentUser['type'] === 'employer'): ?>
                            <p>No messages yet. Send your first message to start the conversation!</p>
                        <?php else: ?>
                            <p>No messages yet. The employer will send you a message soon.</p>
                            <small class="text-info"><i class="fas fa-info-circle"></i> Employers send the first message in conversations</small>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <?php $isSent = $message['sender_id'] == $currentUser['id']; ?>
                        <div class="message <?= $isSent ? 'message-sent' : 'message-received' ?>" data-message-id="<?= $message['id'] ?>">
                            <?php if (!empty($message['attachment_path'])): ?>
                                <?php 
                                $isImage = strpos($message['attachment_type'] ?? '', 'image/') === 0;
                                $attachName = htmlspecialchars($message['attachment_name'] ?? 'Attachment');
                                ?>
                                <div class="message-attachment mb-2">
                                    <?php if ($isImage): ?>
                                        <a href="<?= htmlspecialchars($message['attachment_path']) ?>" target="_blank" class="d-block">
                                            <img src="<?= htmlspecialchars($message['attachment_path']) ?>" alt="<?= $attachName ?>" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= htmlspecialchars($message['attachment_path']) ?>" target="_blank" class="btn btn-sm <?= $isSent ? 'btn-light' : 'btn-outline-primary' ?> d-flex align-items-center" style="max-width: 200px;">
                                            <i class="fas fa-file-download me-2"></i>
                                            <span class="text-truncate"><?= $attachName ?></span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($message['message'])): ?>
                                <div><?= nl2br(htmlspecialchars($message['message'])) ?></div>
                            <?php endif; ?>
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
                <?php $canSendFirstMessage = !empty($messages) || $currentUser['type'] === 'employer'; ?>
                <?php if ($canSendFirstMessage): ?>
                    <?php if ($currentUser['type'] === 'employer'): ?>
                    <div class="mb-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="templateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-alt"></i> Message Templates
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="templateDropdown" style="min-width: 300px;">
                                <li><h6 class="dropdown-header">Quick Templates</h6></li>
                                <li><a class="dropdown-item template-item" href="#" data-template="interview_invite">
                                    <i class="fas fa-calendar-check text-success"></i> Interview Invitation
                                </a></li>
                                <li><a class="dropdown-item template-item" href="#" data-template="follow_up">
                                    <i class="fas fa-clock text-info"></i> Follow Up
                                </a></li>
                                <li><a class="dropdown-item template-item" href="#" data-template="next_steps">
                                    <i class="fas fa-tasks text-primary"></i> Next Steps
                                </a></li>
                                <li><a class="dropdown-item template-item" href="#" data-template="documents_request">
                                    <i class="fas fa-folder text-warning"></i> Request Documents
                                </a></li>
                                <li><a class="dropdown-item template-item" href="#" data-template="welcome">
                                    <i class="fas fa-handshake text-secondary"></i> Welcome Message
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    <form id="messageForm" class="no-loader" onsubmit="sendMessage(event)" enctype="multipart/form-data">
                        <input type="hidden" id="csrfToken" value="<?= getCSRFToken() ?>">
                        <div id="attachmentPreview" class="mb-2" style="display: none;">
                            <div class="d-flex align-items-center bg-light rounded p-2">
                                <i class="fas fa-paperclip me-2 text-primary"></i>
                                <span id="attachmentName" class="text-truncate flex-grow-1" style="max-width: 200px;"></span>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeAttachment()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="input-group">
                            <label class="btn btn-outline-secondary" for="attachmentInput" title="Attach file">
                                <i class="fas fa-paperclip"></i>
                            </label>
                            <input type="file" id="attachmentInput" name="attachment" class="d-none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip" onchange="handleAttachmentSelect(this)">
                            <textarea class="form-control" id="messageInput" placeholder="Type your message..." autocomplete="off" rows="1" style="resize: none; overflow: hidden;"></textarea>
                            <button type="submit" class="btn btn-primary" id="sendBtn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block">Attach: Images, PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP (max 10MB)</small>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info mb-0" id="waitingMessage">
                        <i class="fas fa-info-circle"></i> Please wait for the employer to send the first message before you can reply.
                    </div>
                <?php endif; ?>
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
var jobTitle = <?= json_encode($conversation['job_title']) ?>;
var candidateName = <?= json_encode($otherPartyName) ?>;

var messageTemplates = {
    'interview_invite': 'Hi ' + candidateName + ',\n\nThank you for applying for the ' + jobTitle + ' position. We were impressed by your profile and would like to invite you for an interview.\n\nPlease let us know your availability for the next few days, and we will schedule a convenient time.\n\nLooking forward to speaking with you!',
    'follow_up': 'Hi ' + candidateName + ',\n\nI wanted to follow up regarding your application for the ' + jobTitle + ' position. We are still reviewing candidates and will update you shortly.\n\nIf you have any questions in the meantime, feel free to reach out.\n\nThank you for your patience!',
    'next_steps': 'Hi ' + candidateName + ',\n\nThank you for the great conversation! Here are the next steps in our hiring process:\n\n1. [Step 1]\n2. [Step 2]\n3. [Step 3]\n\nPlease let me know if you have any questions.',
    'documents_request': 'Hi ' + candidateName + ',\n\nAs we proceed with your application for the ' + jobTitle + ' role, we would need you to provide the following documents:\n\n- Updated resume\n- Portfolio/work samples (if applicable)\n- References\n\nPlease share these at your earliest convenience. Thank you!',
    'welcome': 'Hi ' + candidateName + ',\n\nWelcome! Thank you for your interest in the ' + jobTitle + ' position at our company.\n\nI am excited to connect with you and discuss how your skills and experience align with this opportunity.\n\nFeel free to share more about yourself or ask any questions you might have.'
};

document.querySelectorAll('.template-item').forEach(function(item) {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        var templateKey = this.dataset.template;
        if (messageTemplates[templateKey]) {
            messageInput.value = messageTemplates[templateKey];
            messageInput.style.height = 'auto';
            messageInput.style.height = messageInput.scrollHeight + 'px';
            messageInput.focus();
        }
    });
});

messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 150) + 'px';
});

function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function formatTime(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' + 
           date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

var tempMessageId = 0;
var pendingMessages = {};

function addMessage(message, isSent, isPending) {
    var div = document.createElement('div');
    div.className = 'message ' + (isSent ? 'message-sent' : 'message-received');
    if (isPending) {
        div.classList.add('message-pending');
    }
    div.dataset.messageId = message.id;
    
    if (message.attachment_path) {
        var attachDiv = document.createElement('div');
        attachDiv.className = 'message-attachment mb-2';
        
        var isImage = (message.attachment_type || '').indexOf('image/') === 0;
        var attachName = escapeHtml(message.attachment_name || 'Attachment');
        
        if (isImage) {
            attachDiv.innerHTML = '<a href="' + escapeHtml(message.attachment_path) + '" target="_blank" class="d-block">' +
                '<img src="' + escapeHtml(message.attachment_path) + '" alt="' + attachName + '" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">' +
                '</a>';
        } else {
            var btnClass = isSent ? 'btn-light' : 'btn-outline-primary';
            attachDiv.innerHTML = '<a href="' + escapeHtml(message.attachment_path) + '" target="_blank" class="btn btn-sm ' + btnClass + ' d-flex align-items-center" style="max-width: 200px;">' +
                '<i class="fas fa-file-download me-2"></i>' +
                '<span class="text-truncate">' + attachName + '</span>' +
                '</a>';
        }
        div.appendChild(attachDiv);
    }
    
    if (message.message) {
        var content = document.createElement('div');
        content.innerHTML = escapeHtml(message.message).replace(/\n/g, '<br>');
        div.appendChild(content);
    }
    
    var time = document.createElement('div');
    time.className = 'message-time';
    time.textContent = formatTime(message.created_at);
    if (isPending) {
        time.innerHTML += ' <i class="fas fa-clock" style="font-size: 0.6rem;"></i>';
    }
    div.appendChild(time);
    
    var emptyState = chatMessages.querySelector('.text-center.text-muted');
    if (emptyState) emptyState.remove();
    
    chatMessages.appendChild(div);
    scrollToBottom();
    
    return div;
}

function confirmMessage(tempId, realMessage) {
    var pendingEl = document.querySelector('[data-message-id="temp_' + tempId + '"]');
    if (pendingEl) {
        pendingEl.dataset.messageId = realMessage.id;
        pendingEl.classList.remove('message-pending');
        var timeEl = pendingEl.querySelector('.message-time');
        if (timeEl) {
            timeEl.innerHTML = formatTime(realMessage.created_at);
        }
    }
    delete pendingMessages[tempId];
}

function failMessage(tempId, errorMsg) {
    var pendingEl = document.querySelector('[data-message-id="temp_' + tempId + '"]');
    if (pendingEl) {
        pendingEl.classList.remove('message-pending');
        pendingEl.classList.add('message-error');
        var timeEl = pendingEl.querySelector('.message-time');
        if (timeEl) {
            timeEl.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Failed to send';
        }
    }
    delete pendingMessages[tempId];
    if (errorMsg) {
        alert(errorMsg);
    }
}

var attachmentInput = document.getElementById('attachmentInput');
var attachmentPreview = document.getElementById('attachmentPreview');
var attachmentNameEl = document.getElementById('attachmentName');
var selectedFile = null;

function handleAttachmentSelect(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        var maxSize = 10 * 1024 * 1024;
        
        if (file.size > maxSize) {
            alert('File size exceeds 10MB limit');
            input.value = '';
            return;
        }
        
        selectedFile = file;
        attachmentNameEl.textContent = file.name;
        attachmentPreview.style.display = 'block';
    }
}

function removeAttachment() {
    selectedFile = null;
    attachmentInput.value = '';
    attachmentPreview.style.display = 'none';
    attachmentNameEl.textContent = '';
}

function sendMessage(e) {
    e.preventDefault();
    
    var message = messageInput.value.trim();
    var hasAttachment = selectedFile !== null;
    
    if (!message && !hasAttachment) return;
    
    tempMessageId++;
    var currentTempId = tempMessageId;
    
    var tempMessage = {
        id: 'temp_' + currentTempId,
        message: message,
        created_at: new Date().toISOString(),
        sender_id: currentUserId,
        attachment_name: hasAttachment ? selectedFile.name : null,
        attachment_path: null,
        attachment_type: hasAttachment ? selectedFile.type : null
    };
    
    if (hasAttachment && selectedFile.type.indexOf('image/') === 0) {
        tempMessage.attachment_path = URL.createObjectURL(selectedFile);
    }
    
    pendingMessages[currentTempId] = message;
    addMessage(tempMessage, true, true);
    
    var formData = new FormData();
    formData.append('message', message);
    formData.append('csrf_token', document.getElementById('csrfToken').value);
    if (hasAttachment) {
        formData.append('attachment', selectedFile);
    }
    
    messageInput.value = '';
    messageInput.style.height = '38px';
    messageInput.focus();
    removeAttachment();
    
    fetch('/chat/' + conversationId + '/send', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success && data.message) {
            confirmMessage(currentTempId, data.message);
            lastMessageId = Math.max(lastMessageId, data.message.id);
        } else if (data.error) {
            failMessage(currentTempId, data.error);
        }
    })
    .catch(function(err) {
        console.error('Send error:', err);
        failMessage(currentTempId, 'Failed to send message. Please try again.');
    });
}

function pollMessages() {
    fetch('/chat/' + conversationId + '/messages?last_id=' + lastMessageId)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(function(msg) {
                    var existingMsg = document.querySelector('[data-message-id="' + msg.id + '"]');
                    if (!existingMsg && msg.sender_id != currentUserId) {
                        addMessage(msg, false, false);
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    } else if (!existingMsg && msg.sender_id == currentUserId) {
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
