import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Initializes follow/unfollow buttons.
 *
 * @module     local_chuspasocial/follow
 * @copyright  2026 SIS-INF
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export const init = () => {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-action="toggle-follow"]');

        if (!button) {
            return;
        }

        event.preventDefault();

        const component = button.dataset.component;
        const itemid = Number(button.dataset.itemid);

        Ajax.call([{
            methodname: 'local_chuspasocial_toggle_follow',
            args: {
                component,
                itemid,
            },
        }])[0].then(() => {
            const isFollowing = button.getAttribute('aria-pressed') === 'true';
            const nextState = !isFollowing;

            button.setAttribute('aria-pressed', String(nextState));
            button.textContent = nextState ? 'Siguiendo' : 'Seguir';

            return null;
        }).catch(Notification.exception);
    });
};
