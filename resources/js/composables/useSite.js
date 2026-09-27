import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const EMPTY = {
    phone: null, emails: [], departments: [], address: '', scheduleShort: '',
    schedule: '', footerText: '', socials: [], mapUrl: null,
};

/** Контакти сайту з адмінки (спільний prop `site`), у шаблоні: site.phone, site.emails… */
export function useSite() {
    const page = usePage();

    return computed(() => ({ ...EMPTY, ...(page.props.site ?? {}) }));
}
