<div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;">
  @foreach($rows as [$label, $value])
  <div style="background:#f8fafc;border-radius:8px;padding:.55rem .8rem;">
    <div style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.15rem;">{{ $label }}</div>
    <div style="font-size:.88rem;font-weight:700;color:#0f172a;">{{ $value }}</div>
  </div>
  @endforeach
</div>
