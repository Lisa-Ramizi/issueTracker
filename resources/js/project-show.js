import { csrfHeaders } from './helpers';

const STATUS_PROGRESS = {
    open: 15,
    in_progress: 55,
    closed: 100,
};

function initProjectBoard() {
    const board = document.getElementById('project-board');
    if (!board) {
        return;
    }

    let draggedCard = null;
    let dragSourceColumn = null;

    function columnForStatus(status) {
        return board.querySelector(`.kanban__cards[data-status="${status}"]`);
    }

    function updateColumnCount(column) {
        const col = column.closest('.kanban__col');
        const countEl = col?.querySelector('.kanban__count');
        const cards = column.querySelectorAll('.kanban__card');
        if (countEl) {
            countEl.textContent = cards.length;
        }

        const emptyMsg = column.querySelector('.kanban__empty');
        if (cards.length === 0 && !emptyMsg) {
            column.insertAdjacentHTML('beforeend', '<p class="meta kanban__empty" style="margin:0;padding:0.5rem;">No issues here</p>');
        } else if (cards.length > 0) {
            emptyMsg?.remove();
        }
    }

    function updateCardProgress(card, status, progress) {
        card.dataset.status = status;
        const fill = card.querySelector('.issue-card__progress-fill');
        const label = card.querySelector('.issue-card__progress-label');
        if (fill) {
            fill.style.width = `${progress}%`;
        }
        if (label) {
            label.textContent = `${progress}%`;
        }
    }

    board.querySelectorAll('.kanban__card').forEach((card) => {
        card.addEventListener('dragstart', (event) => {
            draggedCard = card;
            dragSourceColumn = card.closest('.kanban__cards');
            card.classList.add('kanban__card--dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.dataset.issueId);
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('kanban__card--dragging');
            card.dataset.wasDragged = '1';
            board.querySelectorAll('.kanban__cards--drag-over').forEach((el) => {
                el.classList.remove('kanban__cards--drag-over');
            });
            draggedCard = null;
            dragSourceColumn = null;
        });

        card.addEventListener('click', (event) => {
            if (card.dataset.wasDragged === '1') {
                event.preventDefault();
                delete card.dataset.wasDragged;
            }
        });
    });

    board.querySelectorAll('.kanban__cards').forEach((column) => {
        column.addEventListener('dragover', (event) => {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            column.classList.add('kanban__cards--drag-over');
        });

        column.addEventListener('dragleave', (event) => {
            if (!column.contains(event.relatedTarget)) {
                column.classList.remove('kanban__cards--drag-over');
            }
        });

        column.addEventListener('drop', async (event) => {
            event.preventDefault();
            column.classList.remove('kanban__cards--drag-over');

            if (!draggedCard) {
                return;
            }

            const newStatus = column.dataset.status;
            const issueId = draggedCard.dataset.issueId;
            const oldStatus = draggedCard.dataset.status;

            if (newStatus === oldStatus) {
                return;
            }

            const sourceColumn = dragSourceColumn;
            column.appendChild(draggedCard);

            if (sourceColumn) {
                updateColumnCount(sourceColumn);
            }
            updateColumnCount(column);

            const progress = STATUS_PROGRESS[newStatus] ?? 15;
            updateCardProgress(draggedCard, newStatus, progress);

            const response = await fetch(`/issues/${issueId}/status`, {
                method: 'PATCH',
                headers: csrfHeaders(),
                body: JSON.stringify({ status: newStatus }),
            });

            if (!response.ok) {
                if (sourceColumn) {
                    sourceColumn.appendChild(draggedCard);
                    updateCardProgress(draggedCard, oldStatus, STATUS_PROGRESS[oldStatus] ?? 15);
                    updateColumnCount(sourceColumn);
                    updateColumnCount(column);
                }
                return;
            }

            const json = await response.json();
            updateCardProgress(draggedCard, json.status, json.progress);
        });
    });
}

document.addEventListener('DOMContentLoaded', initProjectBoard);
