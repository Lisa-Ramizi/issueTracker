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

    let commentsNextUrl = `${commentsUrl}?page=1`;

    function renderComment(comment, prepend = false) {
        const html = `
            <div class="comment">
                <div class="comment__author">${escapeHtml(comment.author_name)}</div>
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
            author_name: formData.get('author_name'),
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
        commentForm.reset();
        commentSuccess.style.display = 'block';

        const count = parseInt(commentCount.textContent, 10) + 1;
        commentCount.textContent = count;
    });

    function renderTagChip(tag) {
        const color = tag.color || '#D8D8D0';
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
    });
}

document.addEventListener('DOMContentLoaded', initIssueShow);
