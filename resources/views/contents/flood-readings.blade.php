<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Flood Readings - AquWatch</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        .glass-card {
            background: rgba(255, 255, 255, 0.36);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-200 via-cyan-200 to-teal-200 p-5 md:p-8">
    <div class="mx-auto max-w-6xl space-y-5">
        <div class="glass-card rounded-3xl p-5 md:p-7 shadow-xl">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-3xl font-extrabold text-cyan-950">Recent Flood Readings</h1>
                    <p class="text-cyan-900/80 mt-1">Optional detailed table for advanced users</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('contents.flood-display') }}" class="px-4 py-2 rounded-xl bg-cyan-700 text-white hover:bg-cyan-800 transition">Back to Flood Display</a>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-3xl p-4 md:p-6 shadow-xl overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-cyan-900/80 uppercase text-xs">
                    <tr>
                        <th class="py-2 pr-4">Received Time (Local)</th>
                        <th class="py-2 pr-4">Sensor</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">S1</th>
                        <th class="py-2 pr-4">S2</th>
                        <th class="py-2 pr-4">S3</th>
                        <th class="py-2 pr-4">Rise (s)</th>
                    </tr>
                </thead>
                <tbody class="text-cyan-950">
                    @forelse ($readings as $reading)
                        <tr class="border-t border-cyan-900/10">
                            <td class="py-2 pr-4" data-iso-time="{{ optional($reading->created_at)->toIso8601String() }}">{{ optional($reading->created_at)->toIso8601String() }}</td>
                            <td class="py-2 pr-4">{{ $reading->sensor_id }}</td>
                            <td class="py-2 pr-4">{{ $reading->status }}</td>
                            <td class="py-2 pr-4">{{ $reading->s1_wet ? 'Wet' : 'Dry' }}</td>
                            <td class="py-2 pr-4">{{ $reading->s2_wet ? 'Wet' : 'Dry' }}</td>
                            <td class="py-2 pr-4">{{ $reading->s3_wet ? 'Wet' : 'Dry' }}</td>
                            <td class="py-2 pr-4">{{ number_format((int) $reading->rise_time_sec) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-3 text-cyan-900">No readings available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-5">
                {{ $readings->onEachSide(1)->links() }}
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-iso-time]').forEach((element) => {
            const iso = element.getAttribute('data-iso-time');
            const parsed = iso ? new Date(iso) : null;

            if (!parsed || Number.isNaN(parsed.getTime())) {
                element.textContent = '-';
                return;
            }

            element.textContent = parsed.toLocaleString();
        });
    </script>
</body>
</html>
