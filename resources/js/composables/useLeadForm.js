import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

/**
 * Форма заявки (POST /forms/{type} → адмінка «Заявки»).
 * website — пастка для ботів: приховане поле, людина його не бачить і не заповнює.
 */
export function useLeadForm(type, fields = {}) {
    const form = useForm({ website: '', ...fields });
    const sent = ref(false);

    function submit(extra = {}) {
        form.transform(data => ({ ...data, ...extra }))
            .post(`/forms/${type}`, {
                preserveScroll: true,
                preserveState: true,
                onSuccess: (page) => {
                    if (page.props.flash?.leadSent === type) {
                        sent.value = true;
                        form.reset();
                    }
                },
            });
    }

    function again() {
        sent.value = false;
        form.clearErrors();
    }

    return { form, sent, submit, again };
}
