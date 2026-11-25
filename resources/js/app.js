import Sortable from 'sortablejs';

const projectFilter = document.getElementById('project-filter');

if (projectFilter) {
    projectFilter.addEventListener('change', () => {
        projectFilter.form.requestSubmit();
    });

    const viewButton = projectFilter.form.querySelector('[data-filter-submit]');

    if (viewButton) {
        viewButton.hidden = true;
    }
}

document.querySelectorAll('[data-delete-task]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm('Delete this task?')) {
            event.preventDefault();
        }
    });
});

const taskList = document.getElementById('task-list');

if (taskList) {
    const status = document.getElementById('order-status');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let previousOrder = [];

    const updatePriorities = () => {
        taskList.querySelectorAll('[data-task-id]').forEach((task, index) => {
            task.querySelector('[data-priority]').textContent = `#${index + 1}`;
        });
    };

    const sortable = Sortable.create(taskList, {
        animation: 150,
        handle: '.drag-handle',
        draggable: '[data-task-id]',
        dataIdAttr: 'data-task-id',
        ghostClass: 'opacity-40',

        onStart() {
            previousOrder = sortable.toArray();
        },

        async onEnd(event) {
            if (event.oldIndex === event.newIndex) {
                return;
            }

            // Prevent overlapping saves from persisting an older order.
            sortable.option('disabled', true);
            status.textContent = 'Saving order…';

            try {
                const response = await fetch(taskList.dataset.reorderUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        task_ids: sortable.toArray().map(Number),
                    }),
                });

                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));

                    throw new Error(
                        data.errors?.task_ids?.[0]
                        ?? 'Could not save the order. Refresh the page and try again.',
                    );
                }

                updatePriorities();
                status.textContent = 'Order saved.';
            } catch (error) {
                sortable.sort(previousOrder);
                updatePriorities();

                status.textContent = error.message
                    || 'Could not confirm the order. Refresh the page and try again.';
            } finally {
                sortable.option('disabled', false);
            }
        },
    });
}
