<?php

namespace Database\Seeders;

use App\Models\FeedbackQuestion;
use App\Models\SessionTemplate;
use App\Models\SessionTemplateBlock;
use Illuminate\Database\Seeder;

class ParadigmSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTemplates();
        $this->seedFeedbackQuestions();
    }

    // ── TEMPLATE ──────────────────────────────────────────────────────────────

    private function seedTemplates(): void
    {
        $templates = [
            [
                'name'      => 'Seduta Tradizionale Standard',
                'paradigm'  => 'traditional',
                'description' => 'Struttura classica FIPAV: tecnica individuale → collettiva → tattica → forma di gioco.',
                'is_system' => true,
                'blocks'    => [
                    ['position'=>1,'block_name'=>'Attivazione / Riscaldamento generale','block_description'=>'Riscaldamento aerobico e articolare.','suggested_duration_minutes'=>15,'block_type'=>'warmup','constraint_focus'=>null],
                    ['position'=>2,'block_name'=>'Tecnica individuale — drill analitico','block_description'=>'Ripetizione analitica del gesto tecnico specifico. Correzione tecnica prescrittiva.','suggested_duration_minutes'=>20,'block_type'=>'technical','constraint_focus'=>null],
                    ['position'=>3,'block_name'=>'Tecnica collettiva — combinazioni','block_description'=>'Collegamento tra fondamentali: es. ricezione → alzata → attacco.','suggested_duration_minutes'=>20,'block_type'=>'technical','constraint_focus'=>null],
                    ['position'=>4,'block_name'=>'Tattica — schemi e situazioni','block_description'=>'Applicazione di schemi tattici definiti. Rotazioni, sistemi di ricezione.','suggested_duration_minutes'=>15,'block_type'=>'tactical','constraint_focus'=>null],
                    ['position'=>5,'block_name'=>'Forma di gioco — applicazione','block_description'=>'Gioco condizionato per applicare le acquisizioni della seduta.','suggested_duration_minutes'=>20,'block_type'=>'game_form','constraint_focus'=>null],
                    ['position'=>6,'block_name'=>'Cool-down / Debriefing','block_description'=>'Defaticamento e briefing tecnico post-seduta.','suggested_duration_minutes'=>10,'block_type'=>'cooldown','constraint_focus'=>null],
                ],
            ],
            [
                'name'      => 'Seduta Ecologica CLA',
                'paradigm'  => 'ecological',
                'description' => 'Constraints-Led Approach con Representative Learning Design. Apprendimento per scoperta attraverso vincoli.',
                'is_system' => true,
                'blocks'    => [
                    ['position'=>1,'block_name'=>'Attivazione percettivo-motoria','block_description'=>'Warm-up con informazioni ambientali reali presenti. Stimola percezione attiva.','suggested_duration_minutes'=>15,'block_type'=>'warmup','constraint_focus'=>null],
                    ['position'=>2,'block_name'=>'Esplorazione vincolo organismo','block_description'=>'Es. un arto limitato, postura modificata, zona corpo vincolata. L\'atleta trova soluzioni motorie.','suggested_duration_minutes'=>20,'block_type'=>'ecological_constraint','constraint_focus'=>'organism'],
                    ['position'=>3,'block_name'=>'Esplorazione vincolo compito','block_description'=>'Regole modificate, obiettivi ridefiniti. Es. obbligo di 3 tocchi, zone di attacco limitate.','suggested_duration_minutes'=>20,'block_type'=>'ecological_constraint','constraint_focus'=>'task'],
                    ['position'=>4,'block_name'=>'Esplorazione vincolo ambiente','block_description'=>'Spazio, tempo, numero giocatori alterati. Aumenta/riduce il campo, cambia il timing.','suggested_duration_minutes'=>15,'block_type'=>'ecological_constraint','constraint_focus'=>'environment'],
                    ['position'=>5,'block_name'=>'Forma di gioco rappresentativa RLD','block_description'=>'Informazioni di gara presenti. Alta rappresentatività. Condizioni simili alla partita.','suggested_duration_minutes'=>20,'block_type'=>'game_form','constraint_focus'=>null],
                    ['position'=>6,'block_name'=>'Riflessione guidata','block_description'=>'Domande aperte: cosa hai percepito? come hai deciso? cosa cambieresti?','suggested_duration_minutes'=>10,'block_type'=>'cooldown','constraint_focus'=>null],
                ],
            ],
            [
                'name'      => 'Seduta Ibrida Libera',
                'paradigm'  => 'hybrid',
                'description' => 'Struttura flessibile che combina drill analitici e approccio ecologico. L\'allenatore decide il bilanciamento.',
                'is_system' => true,
                'blocks'    => [
                    ['position'=>1,'block_name'=>'Attivazione — libera','block_description'=>'Riscaldamento tradizionale o percettivo-motorio a scelta dell\'allenatore.','suggested_duration_minutes'=>15,'block_type'=>'warmup','constraint_focus'=>null],
                    ['position'=>2,'block_name'=>'Blocco A — tipo libero','block_description'=>'Tecnica analitica o vincolo ecologico — scegli in base all\'obiettivo del giorno.','suggested_duration_minutes'=>20,'block_type'=>'free','constraint_focus'=>null],
                    ['position'=>3,'block_name'=>'Blocco B — tipo libero','block_description'=>'Tecnica collettiva o esplorazione vincolo — combina i due approcci.','suggested_duration_minutes'=>20,'block_type'=>'free','constraint_focus'=>null],
                    ['position'=>4,'block_name'=>'Blocco C — tipo libero','block_description'=>'Tattica o forma situazionale — massima flessibilità.','suggested_duration_minutes'=>15,'block_type'=>'free','constraint_focus'=>null],
                    ['position'=>5,'block_name'=>'Forma di gioco — libera','block_description'=>'Gioco con o senza vincoli in base al focus della seduta.','suggested_duration_minutes'=>20,'block_type'=>'game_form','constraint_focus'=>null],
                    ['position'=>6,'block_name'=>'Debriefing','block_description'=>'Feedback prescrittivo e/o interrogativo a seconda dello stile scelto.','suggested_duration_minutes'=>10,'block_type'=>'cooldown','constraint_focus'=>null],
                ],
            ],
        ];

        foreach ($templates as $tpl) {
            $blocks = $tpl['blocks'];
            unset($tpl['blocks']);

            $template = SessionTemplate::firstOrCreate(
                ['name' => $tpl['name'], 'is_system' => true],
                $tpl
            );

            // Ricrea blocchi solo se non esistono ancora
            if ($template->blocks()->count() === 0) {
                foreach ($blocks as $block) {
                    $template->blocks()->create($block);
                }
            }
        }
    }

    // ── DOMANDE FEEDBACK ──────────────────────────────────────────────────────

    private function seedFeedbackQuestions(): void
    {
        $questions = [
            // Both (sempre mostrate)
            ['paradigm'=>'both','question_text'=>'Come valuti l\'intensità della seduta?','question_type'=>'rating','position'=>1],
            ['paradigm'=>'both','question_text'=>'Hai raggiunto gli obiettivi che ti aspettavi?','question_type'=>'rating','position'=>2],

            // Tradizionale
            ['paradigm'=>'traditional','question_text'=>'Hai eseguito il gesto come pianificato?','question_type'=>'rating','position'=>3],
            ['paradigm'=>'traditional','question_text'=>'Dove hai sentito maggiore difficoltà tecnica?','question_type'=>'text','position'=>4],
            ['paradigm'=>'traditional','question_text'=>'Quante ripetizioni ti sono sembrate efficaci?','question_type'=>'rating','position'=>5],

            // Ecologico
            ['paradigm'=>'ecological','question_text'=>'Hai percepito le informazioni giuste per decidere?','question_type'=>'rating','position'=>3],
            ['paradigm'=>'ecological','question_text'=>'In quale momento hai capito cosa fare?','question_type'=>'text','position'=>4],
            ['paradigm'=>'ecological','question_text'=>'Cosa ti ha ostacolato nella lettura della situazione?','question_type'=>'text','position'=>5],
        ];

        foreach ($questions as $q) {
            FeedbackQuestion::firstOrCreate(
                ['paradigm' => $q['paradigm'], 'question_text' => $q['question_text']],
                array_merge($q, ['is_active' => true])
            );
        }
    }
}
