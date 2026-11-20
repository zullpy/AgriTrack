<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantCatalog extends Model
{
    protected $fillable = [
        'key',
        'name',
        'varietas',
        'emoji',
        'cycle',
        'deskripsi',
        'theme',
        'keywords',
        'tahap_semai',
        'tahap_penanaman',
        'tahap_pemeliharaan',
        'tips_penting',
        'hama_penyakit',
        'urutan',
        'aktif',
    ];

    protected $attributes = [
        'cycle' => '—',
        'theme' => 'emerald',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function guides(): HasMany
    {
        return $this->hasMany(PlantCatalogGuide::class)->orderBy('urutan');
    }

    public function hamas(): HasMany
    {
        return $this->hasMany(PlantCatalogHama::class)->orderBy('urutan')->orderBy('id');
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        if (is_numeric($value)) {
            $record = $this->where('id', $value)->first();
            if ($record) {
                return $record;
            }
        }

        return $this->where('key', $value)
            ->orWhere('name', $value)
            ->first() ?? abort(404);
    }

    /**
     * Pecah keywords menjadi array untuk pencocokan crop aktif.
     *
     * @return string[]
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (empty($this->keywords)) {
            return [strtolower($this->name)];
        }

        return array_map('trim', explode(',', strtolower($this->keywords)));
    }

    /**
     * Tailwind badge color classes berdasarkan tema.
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->theme) {
            'rose' => 'bg-rose-100 text-rose-800 border-rose-200',
            'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
            'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'sky' => 'bg-sky-100 text-sky-800 border-sky-200',
            'lime' => 'bg-lime-100 text-lime-800 border-lime-200',
            'teal' => 'bg-teal-100 text-teal-800 border-teal-200',
            default => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan');
    }

    /**
     * Parse raw stage text (tips/hama) into structured note cards like catatan penting.
     *
     * @return array<int, array{title: string, subtitle: string, category: string, content: string}>
     */
    public static function parseTextToCards(?string $rawText, string $type, string $plantName = ''): array
    {
        if (empty($rawText) || trim($rawText) === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($rawText));
        $cards = [];
        $defaultCategory = ($type === 'hama') ? 'Hama & Penyakit' : 'Aturan Lapangan';
        $currentCategory = $defaultCategory;
        $currentCard = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                if ($currentCard !== null && ! empty($currentCard['contentLines'])) {
                    $currentCard['contentLines'][] = '';
                }

                continue;
            }

            // Check if it's a section header (e.g. "1. Hama Utama & Pengendalian:" or "2. Penyakit Utama & Solusi:")
            if (preg_match('/^(?:[0-9]+\.\s*)?(Hama Utama.*?|Penyakit Utama.*?|Aturan Lapangan.*?|Panduan.*?):?$/i', $trimmed, $secMatches)) {
                $currentCategory = rtrim(trim($secMatches[1]), ':');

                continue;
            }

            // Check if this line is an indented line or a sub-section (e.g. "  - Gejala: ...", "- Solusi: ...")
            $isHamaSubSection = ($type === 'hama') && preg_match('/^(?:[-•*]\s*)?(?:Gejala|Solusi|Pengendalian)\s*:/i', $trimmed);
            $isIndented = (bool) preg_match('/^\s{2,}/u', $line);

            if ($currentCard !== null && ($isHamaSubSection || $isIndented)) {
                $currentCard['contentLines'][] = $trimmed;

                continue;
            }

            // Check if it's a new card trigger
            // Case A: Bullet with colon, e.g. "• Thrips (Thrips parvispinus):"
            $hasBulletWithColon = preg_match('/^[•\-*]\s+([^:\n]{3,70}):(.*)$/u', $trimmed, $bcMatches);
            // Case B: Numbered item with colon, e.g. "1. Pasang Ajir:"
            $hasNumWithColon = preg_match('/^[0-9]+\.\s+([^:\n]{3,70}):(.*)$/u', $trimmed, $ncMatches);
            // Case C: Bullet without colon, e.g. "• Hindari penggunaan pupuk Urea..."
            $hasBulletWithoutColon = preg_match('/^[•\-*]\s+(.+)$/u', $trimmed, $bMatches);
            // Case D: Numbered without colon, e.g. "1. Pasang ajir setinggi..."
            $hasNumWithoutColon = preg_match('/^[0-9]+\.\s+(.+)$/u', $trimmed, $nMatches);

            if ($hasBulletWithColon || $hasNumWithColon) {
                if ($currentCard !== null) {
                    $cards[] = self::finalizeCardItem($currentCard, $type, $plantName);
                }
                $m = $hasBulletWithColon ? $bcMatches : $ncMatches;
                $title = trim($m[1]);
                $remainder = trim($m[2]);
                $currentCard = [
                    'title' => $title,
                    'category' => $currentCategory,
                    'contentLines' => $remainder !== '' ? [$remainder] : [],
                ];
            } elseif ($hasBulletWithoutColon || $hasNumWithoutColon) {
                // If it is indented (e.g. "  - Gejala:" or "  - Solusi:"), keep inside current card
                $isIndented = preg_match('/^\s{2,}[•\-*]/u', $line);
                if ($isIndented && $currentCard !== null) {
                    $currentCard['contentLines'][] = $trimmed;
                } else {
                    if ($currentCard !== null) {
                        $cards[] = self::finalizeCardItem($currentCard, $type, $plantName);
                    }
                    $text = trim(($hasBulletWithoutColon ? $bMatches : $nMatches)[1]);
                    $colonPos = strpos($text, ':');

                    if ($colonPos !== false && $colonPos > 2 && $colonPos < 55) {
                        $currentCard = [
                            'title' => trim(substr($text, 0, $colonPos)),
                            'category' => $currentCategory,
                            'contentLines' => array_filter([trim(substr($text, $colonPos + 1))]),
                        ];
                    } elseif (preg_match('/^([^.,;]{10,55})[.,;](.+)$/u', $text, $pMatches)) {
                        $currentCard = [
                            'title' => trim($pMatches[1]),
                            'category' => $currentCategory,
                            'contentLines' => [$text],
                        ];
                    } else {
                        $titlePrefix = ($type === 'hama') ? 'Problem Hama #' : 'Tips Budidaya #';
                        $currentCard = [
                            'title' => $titlePrefix.(count($cards) + 1),
                            'category' => $currentCategory,
                            'contentLines' => [$text],
                        ];
                    }
                }
            } else {
                if ($currentCard !== null) {
                    $currentCard['contentLines'][] = $trimmed;
                } else {
                    $defaultTitle = ($type === 'hama')
                        ? 'Problem & Solusi Hama'.($plantName !== '' ? ' '.$plantName : '')
                        : 'Tips Penting'.($plantName !== '' ? ' '.$plantName : '');

                    $currentCard = [
                        'title' => $defaultTitle,
                        'category' => $currentCategory,
                        'contentLines' => [$trimmed],
                    ];
                }
            }
        }

        if ($currentCard !== null) {
            $cards[] = self::finalizeCardItem($currentCard, $type, $plantName);
        }

        return $cards;
    }

    /**
     * Finalize card array.
     *
     * @param  array{title: string, category: string, contentLines: string[]}  $card
     * @return array{id: ?int, title: string, judul: string, subtitle: string, category: string, kategori: string, gejala: ?string, solusi: ?string, content: string, foto: array, foto_urls: array, foto_count: int, urutan: int, model: ?PlantCatalogHama}
     */
    protected static function finalizeCardItem(array $card, string $type, string $plantName = ''): array
    {
        $content = trim(implode("\n", $card['contentLines']));
        $content = preg_replace("/\n{3,}/", "\n\n", $content);

        $subtitle = ($card['category'] ?? ($type === 'hama' ? 'Hama & Penyakit' : 'Aturan Lapangan'));
        if ($plantName !== '') {
            $subtitle .= ' • '.$plantName;
        }

        $gejala = null;
        $solusi = null;
        if ($type === 'hama') {
            if (preg_match('/(?:^|\n)\s*(?:[-•*]\s*)?Gejala(?:\s+Serangan)?\s*:\s*(.*?)(?=\n\s*(?:[-•*]\s*)?(?:Solusi|Pengendalian)(?:\s*(?:&amp;|&|\/)\s*Pengendalian)?\s*:|$)/is', $content, $gMatch)) {
                $gejala = trim($gMatch[1]);
            }
            if (preg_match('/(?:^|\n)\s*(?:[-•*]\s*)?(?:Solusi|Pengendalian)(?:\s*(?:&amp;|&|\/)\s*Pengendalian)?\s*:\s*(.*)/is', $content, $sMatch)) {
                $solusi = trim($sMatch[1]);
            }
        }

        $title = $card['title'] ?: ($type === 'hama' ? 'Pengendalian Hama' : 'Tips Lapangan');

        return [
            'id' => null,
            'title' => $title,
            'judul' => $title,
            'subtitle' => $subtitle,
            'category' => $card['category'] ?? '',
            'kategori' => $card['category'] ?? '',
            'gejala' => $gejala,
            'solusi' => $solusi,
            'content' => $content ?: $title,
            'foto' => [],
            'foto_urls' => [],
            'foto_count' => 0,
            'urutan' => 0,
            'model' => null,
        ];
    }

    /**
     * Parse tips_penting into structured note cards.
     *
     * @return array<int, array{title: string, subtitle: string, category: string, content: string}>
     */
    public function getStructuredTipsAttribute(): array
    {
        return self::parseTextToCards($this->tips_penting, 'tips', $this->name);
    }

    /**
     * Parse hama_penyakit into structured note cards.
     *
     * @return array<int, array{id: ?int, title: string, judul: string, subtitle: string, category: string, kategori: string, gejala: ?string, solusi: ?string, content: string, foto: array, foto_urls: array, foto_count: int, urutan: int, model: ?PlantCatalogHama}>
     */
    public function getStructuredHamaAttribute(): array
    {
        $hamas = $this->relationLoaded('hamas') ? $this->hamas : $this->hamas()->get();
        if ($hamas->isNotEmpty()) {
            $parsedFallback = null;

            return $hamas->filter(function (PlantCatalogHama $hama) {
                return ! in_array(strtolower(trim($hama->judul)), ['gejala', 'solusi'], true);
            })->values()->map(function (PlantCatalogHama $hama) use (&$parsedFallback) {
                $gejala = $hama->gejala;
                $solusi = $hama->solusi;

                if ((empty($gejala) || empty($solusi)) && ! empty($this->hama_penyakit)) {
                    if ($parsedFallback === null) {
                        $parsedFallback = self::parseTextToCards($this->hama_penyakit, 'hama', $this->name);
                    }
                    foreach ($parsedFallback as $pCard) {
                        $pTitle = strtolower(trim($pCard['title'] ?? ''));
                        $hTitle = strtolower(trim($hama->judul));
                        if ($pTitle === $hTitle || stripos($pTitle, $hTitle) !== false || stripos($hTitle, $pTitle) !== false) {
                            if (empty($gejala)) {
                                $gejala = $pCard['gejala'] ?? null;
                            }
                            if (empty($solusi)) {
                                $solusi = $pCard['solusi'] ?? null;
                            }
                            break;
                        }
                    }
                }

                $subtitle = $hama->kategori ? ($hama->kategori.' • '.$this->name) : ('Hama & Penyakit • '.$this->name);
                $content = trim(
                    ($gejala ? 'Gejala: '.$gejala."\n\n" : '').
                    ($solusi ? 'Solusi: '.$solusi : '')
                );

                return [
                    'id' => $hama->id,
                    'title' => $hama->judul,
                    'judul' => $hama->judul,
                    'subtitle' => $subtitle,
                    'category' => $hama->kategori,
                    'kategori' => $hama->kategori,
                    'gejala' => $gejala,
                    'solusi' => $solusi,
                    'content' => $content ?: $hama->judul,
                    'foto' => $hama->foto ?? [],
                    'foto_urls' => $hama->foto_urls,
                    'foto_count' => $hama->foto_count,
                    'urutan' => $hama->urutan,
                    'model' => $hama,
                ];
            })->all();
        }

        return self::parseTextToCards($this->hama_penyakit, 'hama', $this->name);
    }

    /**
     * Parse stage text (semai, penanaman, pemeliharaan) into structured step items like menu tahapan.
     *
     * @return array<int, array{nomor: string, judul: string, deskripsi: string, waktu: ?string, tips: ?string, badge: string}>
     */
    public static function parseTextToSteps(?string $rawText, string $stageType, string $plantName = ''): array
    {
        if (empty($rawText) || trim($rawText) === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($rawText));
        $steps = [];
        $currentStep = null;
        $stageLabels = [
            'semai' => 'Tahap Semai',
            'penanaman' => 'Tahap Penanaman',
            'pemeliharaan' => 'Tahap Perawatan',
        ];
        $stageBadge = $stageLabels[$stageType] ?? 'Tahapan SOP';

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                if ($currentStep !== null && ! empty($currentStep['lines'])) {
                    $currentStep['lines'][] = '';
                }

                continue;
            }

            // Check if line starts a new step:
            // e.g. "1. Judul Langkah:" or "1. Judul Langkah" or "Langkah 1: Judul"
            $isNumbered = preg_match('/^(?:Langkah|Tahap)?\s*([0-9]+)[\.\:\)]\s*(.+)$/ui', $trimmed, $numMatches);

            if ($isNumbered) {
                if ($currentStep !== null) {
                    $steps[] = self::finalizeStepItem($currentStep, count($steps) + 1, $stageBadge, $plantName);
                }
                $stepNum = $numMatches[1];
                $titleAndRest = trim($numMatches[2]);
                $colonPos = strpos($titleAndRest, ':');
                if ($colonPos !== false && $colonPos < 65) {
                    $judul = trim(substr($titleAndRest, 0, $colonPos));
                    $rest = trim(substr($titleAndRest, $colonPos + 1));
                    $currentStep = [
                        'nomor' => (string) $stepNum,
                        'judul' => $judul ?: $titleAndRest,
                        'lines' => $rest !== '' ? [$rest] : [],
                    ];
                } else {
                    $currentStep = [
                        'nomor' => (string) $stepNum,
                        'judul' => rtrim($titleAndRest, ':'),
                        'lines' => [],
                    ];
                }
            } else {
                if ($currentStep !== null) {
                    $currentStep['lines'][] = $trimmed;
                } else {
                    $currentStep = [
                        'nomor' => '1',
                        'judul' => ($stageLabels[$stageType] ?? 'Tahapan').($plantName ? ' '.$plantName : ''),
                        'lines' => [$trimmed],
                    ];
                }
            }
        }

        if ($currentStep !== null) {
            $steps[] = self::finalizeStepItem($currentStep, count($steps) + 1, $stageBadge, $plantName);
        }

        return $steps;
    }

    /**
     * Finalize step item.
     *
     * @param  array{nomor: string, judul: string, lines: string[]}  $step
     * @return array{nomor: string, judul: string, deskripsi: string, waktu: ?string, tips: ?string, badge: string}
     */
    protected static function finalizeStepItem(array $step, int $fallbackIndex, string $stageBadge, string $plantName = ''): array
    {
        $nomor = ! empty($step['nomor']) ? $step['nomor'] : (string) $fallbackIndex;
        $judul = ! empty($step['judul']) ? $step['judul'] : ('Langkah '.$nomor);

        $content = trim(implode("\n", $step['lines'] ?? []));
        $content = preg_replace("/\n{3,}/", "\n\n", $content);

        $waktu = null;
        if (preg_match('/(?:waktu|durasi|umur|interval|fase)\s*:\s*([^,\n\(\)]+)/i', $content, $timeMatch)) {
            $waktu = trim($timeMatch[1]);
        }

        return [
            'nomor' => $nomor,
            'judul' => $judul,
            'deskripsi' => $content,
            'waktu' => $waktu,
            'tips' => null,
            'badge' => $stageBadge,
        ];
    }

    /**
     * Parse tahap_semai into structured steps like menu tahapan.
     *
     * @return array<int, array{nomor: string, judul: string, deskripsi: string, waktu: ?string, tips: ?string, badge: string}>
     */
    public function getStructuredSemaiAttribute(): array
    {
        return self::parseTextToSteps($this->tahap_semai, 'semai', $this->name);
    }

    /**
     * Parse tahap_penanaman into structured steps like menu tahapan.
     *
     * @return array<int, array{nomor: string, judul: string, deskripsi: string, waktu: ?string, tips: ?string, badge: string}>
     */
    public function getStructuredPenanamanAttribute(): array
    {
        return self::parseTextToSteps($this->tahap_penanaman, 'penanaman', $this->name);
    }

    /**
     * Parse tahap_pemeliharaan into structured steps like menu tahapan.
     *
     * @return array<int, array{nomor: string, judul: string, deskripsi: string, waktu: ?string, tips: ?string, badge: string}>
     */
    public function getStructuredPemeliharaanAttribute(): array
    {
        return self::parseTextToSteps($this->tahap_pemeliharaan, 'pemeliharaan', $this->name);
    }

    /**
     * Compile structured step items into text format.
     *
     * @param  array<int, array{nomor?: string, judul?: string, deskripsi?: string}>  $steps
     */
    public static function compileStepsToText(array $steps): string
    {
        $blocks = [];
        $index = 1;

        foreach ($steps as $step) {
            $nomor = ! empty($step['nomor']) ? trim((string) $step['nomor']) : (string) $index;
            $judul = trim((string) ($step['judul'] ?? ''));
            $deskripsi = trim((string) ($step['deskripsi'] ?? ''));
            $waktu = trim((string) ($step['waktu'] ?? ''));

            if ($judul === '' && $deskripsi === '') {
                continue;
            }

            if ($waktu !== '' && ! preg_match('/(?:waktu|durasi|umur|interval|fase)\s*:/i', $deskripsi)) {
                $deskripsi = "Waktu: {$waktu}\n".$deskripsi;
            }

            $header = $judul !== '' ? "{$nomor}. {$judul}:" : "{$nomor}.";
            if ($deskripsi !== '') {
                $blocks[] = "{$header}\n{$deskripsi}";
            } else {
                $blocks[] = $header;
            }

            $index++;
        }

        return implode("\n\n", $blocks);
    }

    /**
     * Compile structured tip items into text format.
     *
     * @param  array<int, array{judul?: string, title?: string, content?: string, deskripsi?: string}>  $tips
     */
    public static function compileTipsToText(array $tips): string
    {
        $blocks = [];
        $index = 1;

        foreach ($tips as $tip) {
            $judul = trim((string) ($tip['judul'] ?? ($tip['title'] ?? '')));
            $content = trim((string) ($tip['content'] ?? ($tip['deskripsi'] ?? '')));

            if ($judul === '' && $content === '') {
                continue;
            }

            if ($judul !== '' && $content !== '') {
                $blocks[] = "{$index}. {$judul}:\n{$content}";
            } elseif ($judul !== '') {
                $blocks[] = "{$index}. {$judul}";
            } else {
                $blocks[] = "• {$content}";
            }

            $index++;
        }

        return implode("\n\n", $blocks);
    }

    /**
     * Compile structured problem & hama items into text format.
     *
     * @param  array<int, array{judul?: string, title?: string, kategori?: string, gejala?: string, solusi?: string}>  $hamas
     */
    public static function compileHamaToText(array $hamas): string
    {
        $blocks = [];
        $index = 1;

        foreach ($hamas as $hama) {
            $judul = trim((string) ($hama['judul'] ?? ($hama['title'] ?? '')));
            $gejala = trim((string) ($hama['gejala'] ?? ''));
            $solusi = trim((string) ($hama['solusi'] ?? ''));

            if ($judul === '' && $gejala === '' && $solusi === '') {
                continue;
            }

            $lines = [];
            if ($gejala !== '') {
                $lines[] = "Gejala:\n{$gejala}";
            }
            if ($solusi !== '') {
                $lines[] = "Solusi:\n{$solusi}";
            }

            $body = implode("\n\n", $lines);
            if ($judul !== '' && $body !== '') {
                $blocks[] = "{$index}. {$judul}:\n{$body}";
            } elseif ($judul !== '') {
                $blocks[] = "{$index}. {$judul}";
            } else {
                $blocks[] = $body;
            }

            $index++;
        }

        return implode("\n\n", $blocks);
    }
}
