@php
    $materiaIdsAtuais = $turma ? $turma->materias->pluck('id')->all() : [];
    $quantidadesAtuais = $turma ? $turma->materias->mapWithKeys(fn ($m) => [$m->id => $m->pivot->quantidade_aulas]) : collect();
@endphp

<hr class="my-4">

<h5 class="mb-1">Matriz curricular</h5>
<p class="text-muted small">Quais disciplinas essa série/turma tem e quantas aulas semanais cada uma exige.</p>

@if($materias->isEmpty())
    <div class="alert alert-warning">
        Nenhuma disciplina cadastrada ainda. <a href="/grade/materias">Cadastre as disciplinas</a> antes de montar a matriz curricular.
    </div>
@else
    <div class="mb-3">
        <label class="form-label">Disciplinas</label>
        <select name="materias[]" id="selectMaterias" class="form-select" multiple style="width:100%">
            @foreach($materias as $materia)
                <option value="{{ $materia->id }}" {{ in_array($materia->id, old('materias', $materiaIdsAtuais)) ? 'selected' : '' }}>
                    {{ $materia->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div id="linhasQuantidade" class="mb-3"></div>

    <div class="alert" id="alertaTotal">
        Total de aulas semanais alocado: <strong id="totalAlocado">0</strong> / <span id="totalMaximo">0</span>
    </div>

    <script>
        window.materiasDisponiveis = @json($materias->map(fn ($m) => ['id' => $m->id, 'nome' => $m->nome]));
        window.quantidadesAtuais = @json(old('quantidades', $quantidadesAtuais));
    </script>
@endif
