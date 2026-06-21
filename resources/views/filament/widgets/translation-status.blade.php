<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Заповненість перекладів
        </x-slot>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="py-2 text-left font-medium text-gray-500">Розділ</th>
                    <th class="py-2 text-center font-medium text-gray-500">Всього</th>
                    @foreach($languages as $lang)
                        <th class="py-2 text-center font-medium text-gray-500 uppercase">{{ $lang->code }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="py-2 font-medium">{{ $row['label'] }}</td>
                        <td class="py-2 text-center text-gray-500">{{ $row['total'] }}</td>
                        @foreach($row['languages'] as $stat)
                            <td class="py-2 text-center">
                                @if($row['total'] === 0)
                                    <span class="text-gray-400">—</span>
                                @elseif($stat['done'])
                                    <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                                        <x-heroicon-o-check-circle class="w-4 h-4"/>
                                        {{ $stat['filled'] }}/{{ $stat['total'] }}
                                    </span>
                                @elseif($stat['filled'] === 0)
                                    <span class="inline-flex items-center gap-1 text-danger-600 dark:text-danger-400">
                                        <x-heroicon-o-x-circle class="w-4 h-4"/>
                                        0/{{ $stat['total'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-warning-600 dark:text-warning-400">
                                        <x-heroicon-o-exclamation-circle class="w-4 h-4"/>
                                        {{ $stat['filled'] }}/{{ $stat['total'] }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::section>
</x-filament-widgets::widget>
