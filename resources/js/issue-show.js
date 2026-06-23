import { csrfHeaders, escapeHtml } from './helpers';

function initIssueShow() {
    const root = document.getElementById('issue-show');
    if (!root) {
        return;
    }

    const issueId = root.dataset.issueId;
    const commentsUrl = root.dataset.commentsUrl;
    const commentStoreUrl = root.dataset.commentStoreUrl;

    const commentsList = document.getElementById('comments-list');
    const commentsEmpty = document.getElementById('comments-empty');
    const loadMoreBtn = document.getElementById('comments-load-more');
    const commentCount = document.getElementById('comment-count');
    const commentForm = document.getElementById('comment-form');
    const commentSuccess = document.getElementById('comment-success');

    const tagsList = document.getElementById('issue-tags');
    const tagsEmpty = document.getElementById('tags-empty');
    const tagAttachSelect = document.getElementById('tag-attach-select');
    const tagAttachBtn = document.getElementById('tag-attach-btn');
    const tagAttachGroup = document.getElementById('tag-attach-group');

    const membersList = document.getElementById('issue-members');
    const membersEmpty = document.getElementById('members-empty');
    const memberAttachSelect = document.getElementById('member-attach-select');
    const memberAttachBtn = document.getElementById('member-attach-btn');
    const memberAttachGroup = document.getElementById('member-attach-group');

    const activityList = document.getElementById('activity-list');
    const activityEmpty = document.getElementById('activity-empty');

    let commentsNextUrl = `${commentsUrl}?page=1`;

    function prependActivity(activity) {
        if (!activity || !activityList) {
            return;
        }

        activityEmpty?.remove();

        const html = `
            <li class="activity-item" data-activity-id="${activity.id}">
                <div class="activity-item__dot"></div>
                <div class="activity-item__body">
                    <p class="activity-item__message">${escapeHtml(activity.message)}</p>
                    <p class="activity-item__meta">
                        <span class="activity-item__user">${escapeHtml(activity.user_name)}</span>
                        <span class="activity-item__time">${escapeHtml(activity.created_at_human || 'just now')}</span>
                    </p>
                </div>
            </li>
        `;

        activityList.insertAdjacentHTML('afterbegin', html);
    }

    function renderComment(comment, prepend = false) {
        const deleteBtn = comment.can_delete
            ? `<button type="button" class="comment__delete" data-comment-id="${comment.id}" title="Delete comment">Delete</button>`
            : '';

        const html = `
            <div class="comment" data-comment-id="${comment.id}">
                <div class="comment__header">
                    <div class="comment__author">${escapeHtml(comment.author_name)}</div>
                    ${deleteBtn}
                </div>
                <div class="comment__time">${escapeHtml(comment.created_at_human || '')}</div>
                <p class="comment__body">${escapeHtml(comment.body)}</p>
            </div>
        `;

        if (commentsEmpty) {
            commentsEmpty.remove();
        }

        if (prepend) {
            commentsList.insertAdjacentHTML('afterbegin', html);
        } else {
            commentsList.insertAdjacentHTML('beforeend', html);
        }
    }

    async function loadComments(url, append = false) {
        const response = await fetch(url, { headers: csrfHeaders(false) });
        const json = await response.json();

        if (!append) {
            commentsList.innerHTML = '';
        }

        if (json.data.length === 0 && !append) {
            commentsList.innerHTML = '<p class="meta" id="comments-empty" style="margin:0;">No comments yet. Be the first!</p>';
        } else {
            json.data.forEach((comment) => renderComment(comment, false));
        }

        commentsNextUrl = json.next_page_url;

        if (loadMoreBtn) {
            loadMoreBtn.style.display = commentsNextUrl ? 'inline-block' : 'none';
        }
    }

    loadComments(commentsNextUrl);

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', async () => {
            if (!commentsNextUrl) {
                return;
            }

            loadMoreBtn.disabled = true;
            await loadComments(commentsNextUrl, true);
            loadMoreBtn.disabled = false;
        });
    }

    commentsList?.addEventListener('click', async (event) => {
        const button = event.target.closest('.comment__delete');
        if (!button) {
            return;
        }

        if (!confirm('Delete this comment?')) {
            return;
        }

        const commentId = button.dataset.commentId;
        const response = await fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: csrfHeaders(false),
        });

        if (!response.ok) {
            return;
        }

        button.closest('.comment')?.remove();

        const count = Math.max(0, parseInt(commentCount.textContent, 10) - 1);
        commentCount.textContent = count;

        if (commentsList.querySelectorAll('.comment').length === 0) {
            commentsList.innerHTML = '<p class="meta" id="comments-empty" style="margin:0;">No comments yet. Be the first!</p>';
        }
    });

    function clearCommentErrors() {
        commentForm.querySelectorAll('.form-error').forEach((el) => el.remove());
    }

    function showCommentErrors(errors) {
        clearCommentErrors();

        Object.entries(errors).forEach(([field, messages]) => {
            const input = commentForm.querySelector(`[name="${field}"]`);
            if (!input) {
                return;
            }

            const error = document.createElement('div');
            error.className = 'form-error';
            error.textContent = messages[0];
            input.parentElement.appendChild(error);
        });
    }

    commentForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearCommentErrors();
        commentSuccess.style.display = 'none';

        const formData = new FormData(commentForm);
        const payload = {
            body: formData.get('body'),
        };

        const response = await fetch(commentStoreUrl, {
            method: 'POST',
            headers: csrfHeaders(),
            body: JSON.stringify(payload),
        });

        if (response.status === 422) {
            const json = await response.json();
            showCommentErrors(json.errors);
            return;
        }

        if (!response.ok) {
            return;
        }

        const json = await response.json();
        renderComment(json.comment, true);
        prependActivity(json.activity);
        commentForm.reset();
        commentSuccess.style.display = 'block';

        const count = parseInt(commentCount.textContent, 10) + 1;
        commentCount.textContent = count;
    });

    function renderTagChip(tag) {
        const color = tag.color || '#F5D0DC';
        const wrapper = document.createElement('span');
        wrapper.className = 'tag-chip-wrapper';
        wrapper.dataset.tagId = tag.id;
        wrapper.innerHTML = `
            <span class="tag-chip" style="background-color: ${color}">${escapeHtml(tag.name)}</span>
            <button type="button" class="tag-chip__remove" data-tag-id="${tag.id}" title="Remove tag">&times;</button>
        `;
        return wrapper;
    }

    function addAvailableTagOption(tag) {
        if (!tagAttachSelect) {
            return;
        }

        const option = document.createElement('option');
        option.value = tag.id;
        option.textContent = tag.name;
        tagAttachSelect.appendChild(option);

        if (tagAttachGroup) {
            tagAttachGroup.style.display = '';
        }
    }

    function removeAvailableTagOption(tagId) {
        if (!tagAttachSelect) {
            return;
        }

        const option = tagAttachSelect.querySelector(`option[value="${tagId}"]`);
        option?.remove();

        if (tagAttachSelect.options.length === 0 && tagAttachGroup) {
            tagAttachGroup.style.display = 'none';
        }
    }

    tagsList?.addEventListener('click', async (event) => {
        const button = event.target.closest('.tag-chip__remove');
        if (!button) {
            return;
        }

        const tagId = button.dataset.tagId;
        const response = await fetch(`/issues/${issueId}/tags/${tagId}/detach`, {
            method: 'DELETE',
            headers: csrfHeaders(false),
        });

        if (!response.ok) {
            return;
        }

        const json = await response.json();
        prependActivity(json.activity);

        const wrapper = button.closest('.tag-chip-wrapper');
        const tagName = wrapper.querySelector('.tag-chip').textContent;
        const tagColor = wrapper.querySelector('.tag-chip').style.backgroundColor;

        wrapper.remove();

        if (tagsList.children.length === 0) {
            tagsList.innerHTML = '<span class="meta" id="tags-empty">No tags yet.</span>';
        }

        addAvailableTagOption({ id: tagId, name: tagName, color: tagColor });
    });

    tagAttachBtn?.addEventListener('click', async () => {
        const tagId = tagAttachSelect.value;
        if (!tagId) {
            return;
        }

        const response = await fetch(`/issues/${issueId}/tags/${tagId}/attach`, {
            method: 'POST',
            headers: csrfHeaders(false),
        });

        if (!response.ok) {
            return;
        }

        const json = await response.json();
        const tag = json.tag;

        if (tagsEmpty) {
            tagsEmpty.remove();
        }

        tagsList.appendChild(renderTagChip(tag));
        removeAvailableTagOption(tag.id);
        tagAttachSelect.value = '';
        prependActivity(json.activity);
    });

    function renderMemberChip(user) {
        const wrapper = document.createElement('span');
        wrapper.className = 'member-chip-wrapper';
        wrapper.dataset.userId = user.id;
        wrapper.innerHTML = `
            <span class="member-chip">${escapeHtml(user.name)}</span>
            <button type="button" class="member-chip__remove" data-user-id="${user.id}" title="Remove member">&times;</button>
        `;
        return wrapper;
    }

    function addAvailableMemberOption(user) {
        if (!memberAttachSelect) {
            return;
        }

        const option = document.createElement('option');
        option.value = user.id;
        option.textContent = user.name;
        memberAttachSelect.appendChild(option);

        if (memberAttachGroup) {
            memberAttachGroup.style.display = '';
        }
    }

    function removeAvailableMemberOption(userId) {
        if (!memberAttachSelect) {
            return;
        }

        const option = memberAttachSelect.querySelector(`option[value="${userId}"]`);
        option?.remove();

        if (memberAttachSelect.options.length <= 1 && memberAttachGroup) {
            memberAttachGroup.style.display = 'none';
        }
    }

    membersList?.addEventListener('click', async (event) => {
        const button = event.target.closest('.member-chip__remove');
        if (!button) {
            return;
        }

        const userId = button.dataset.userId;
        const response = await fetch(`/issues/${issueId}/users/${userId}/detach`, {
            method: 'DELETE',
            headers: csrfHeaders(false),
        });

        if (!response.ok) {
            return;
        }

        const json = await response.json();
        prependActivity(json.activity);

        const wrapper = button.closest('.member-chip-wrapper');
        const userName = wrapper.querySelector('.member-chip').textContent;

        wrapper.remove();

        if (membersList.children.length === 0) {
            membersList.innerHTML = '<span class="meta" id="members-empty">No members assigned yet.</span>';
        }

        addAvailableMemberOption({ id: userId, name: userName });
    });

    memberAttachBtn?.addEventListener('click', async () => {
        const userId = memberAttachSelect.value;
        if (!userId) {
            return;
        }

        const response = await fetch(`/issues/${issueId}/users/${userId}/attach`, {
            method: 'POST',
            headers: csrfHeaders(false),
        });

        if (!response.ok) {
            return;
        }

        const json = await response.json();
        const user = json.user;

        if (membersEmpty) {
            membersEmpty.remove();
        }

        membersList.appendChild(renderMemberChip(user));
        removeAvailableMemberOption(user.id);
        memberAttachSelect.value = '';
        prependActivity(json.activity);
    });
}

document.addEventListener('DOMContentLoaded', initIssueShow);
