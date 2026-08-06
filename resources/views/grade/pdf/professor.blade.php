<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1a3a6b; }
        h1 { font-size: 16px; margin-bottom: 0; }
        h2 { font-size: 13px; color: #555; margin-top: 4px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: center; }
        th { background: #1a3a6b; color: #fff; }
        .turno { font-weight: bold; margin-top: 10px; margin-bottom: 4px; }
    </style>
</head>
<body>
    <h1>{{ $escola->nome ?? '' }}</h1>
    <h2>Grade individual — {{ $professor->nome }}</h2>

    @php $diasNomes = [1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado']; @endphp

    @foreach(['manha' => ['Manhã', $maxManha], 'tarde' => ['Tarde', $maxTarde]] as $turno => [$rotulo, $max])
        @if($max > 0)
            <div class="turno">{{ $rotulo }}</div>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        @foreach($diasNomes as $dia => $nome)
                            <th>{{ $nome }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @for($numero = 1; $numero <= $max; $numero++)
                        <tr>
                            <td>{{ $numero }}ª</td>
                            @foreach($diasNomes as $dia => $nome)
                                @php $horario = $grade[$turno][$numero][$dia] ?? null; @endphp
                                <td>
                                    @if($horario)
                                        {{ $horario->turma->nome }}<br>
                                        <small>{{ $horario->materia->nome }}</small>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        @endif
    @endforeach
</body>
</html>
