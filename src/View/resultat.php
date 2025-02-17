<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Données Météo</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .chart-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
        }
        canvas {
            width: 100% !important;
            height: 400px !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Données Météo</h1>

        <table>
            <tr>
                <th>Nom</th>
                <th>Date</th>
                <?php foreach ($filtre as $key => $value) {
                    if ($value != '') {
                        echo "<th>" . htmlspecialchars($key) . "</th>";
                    }
                } ?>
            </tr>
            <?php foreach ($resultat as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <?php foreach ($filtre as $key => $value) {
                        if ($value != '') {
                            echo "<td>" . htmlspecialchars($row[$key]) . "</td>";
                        }
                    } ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <div id="chartsContainer"></div>
    </div>

    <script>
        const resultat = <?php echo json_encode($resultat); ?>;
        const filtre = <?php echo json_encode($filtre); ?>;
        const chartsContainer = document.getElementById('chartsContainer');

        const keyLabels = {
            'tc': 'Température (°C)',
            'dd': 'Direction du vent',
            'ff': 'Vitesse du vent (m/s)',
            'u': 'Humidité (%)',
            'rr24': 'Précipitation (mm)',
            'phenspe1': 'Phénomènes spéciaux'
        };

        const timeIntervals = ['00:00', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '21:00'];

        const extractRoundedHour = (dateString) => {
            const date = new Date(dateString);
            return `${Math.floor(date.getHours() / 3) * 3}:00`.padStart(5, '0');
        };

        const extractDate = (dateString) => {
            return new Date(dateString).toISOString().split('T')[0];
        };

        const groupDataByHour = (data, key) => {
            const grouped = Object.fromEntries(timeIntervals.map(hour => [hour, []]));
            data.forEach(item => {
                const hour = extractRoundedHour(item['date']);
                grouped[hour].push(parseFloat(item[key]));
            });
            return {
                labels: timeIntervals,
                values: timeIntervals.map(hour => 
                    grouped[hour].length ? grouped[hour].reduce((sum, val) => sum + val, 0) / grouped[hour].length : 0
                )
            };
        };

        const groupDataByDay = (data, key) => {
            const grouped = {};
            data.forEach(item => {
                const day = extractDate(item['date']);
                if (!grouped[day]) grouped[day] = [];
                grouped[day].push(parseFloat(item[key]));
            });
            return Object.keys(grouped).map(day => ({
                label: day,
                value: grouped[day].reduce((sum, val) => sum + val, 0) / grouped[day].length
            }));
        };

        const groupDataByCity = (data, key) => {
            const grouped = {};
            data.forEach(item => {
                const city = item['nom'];
                if (!grouped[city]) grouped[city] = [];
                grouped[city].push(parseFloat(item[key]));
            });
            return Object.keys(grouped).map(city => ({
                label: city,
                value: grouped[city].reduce((sum, val) => sum + val, 0) / grouped[city].length
            }));
        };

        function createChart(ctx, title, labels, data, index) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: title,
                        data: data,
                        borderColor: `hsl(${index * 60}, 70%, 50%)`,
                        backgroundColor: `hsla(${index * 60}, 70%, 50%, 0.5)`
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: title,
                            font: { size: 18 }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        Object.keys(filtre).forEach((key, index) => {
            if (filtre[key] !== '') {
                const displayKey = keyLabels[key] || key;
                const dates = [...new Set(resultat.map(item => extractDate(item.date)))];
                const cities = [...new Set(resultat.map(item => item.nom))];

                const chartCanvas = document.createElement('canvas');
                chartCanvas.id = `chart-${key}`;
                const chartContainer = document.createElement('div');
                chartContainer.className = 'chart-container';
                chartContainer.appendChild(chartCanvas);
                chartsContainer.appendChild(chartContainer);

                const ctx = chartCanvas.getContext('2d');

                if (cities.length > 1) {
                    const cityData = groupDataByCity(resultat, key);
                    createChart(ctx, `Moyenne par ville pour ${displayKey}`, cityData.map(item => item.label), cityData.map(item => item.value), index);
                }
                if (dates.length > 1) {
                    const dailyData = groupDataByDay(resultat, key);
                    createChart(ctx, `Moyenne journalière pour ${displayKey}`, dailyData.map(item => item.label), dailyData.map(item => item.value), index);
                }
                if (dates.length <= 1 && cities.length <= 1) {
                    const hourlyData = groupDataByHour(resultat, key);
                    createChart(ctx, `Graphique pour ${displayKey} par heure`, hourlyData.labels, hourlyData.values, index);
                }
            }
        });
    </script>
</body>
</html>
