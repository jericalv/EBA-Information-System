<?php
namespace App\Services;

class WordFilterService
{
    protected array $blockedWords = [
    'puta', 'putang', 'putangina', 'tangina', 'taena', 'taina',
    'gago', 'gaga', 'tarantado', 'tarantada',
    'ulol', 'bobo', 'tanga', 'engot', 'unggoy',
    'punyeta', 'bwisit', 'leche', 'lintik',
    'hayop', 'hinayupak', 'kupal', 'hudas',
    'inutil', 'siraulo', 'abnoy', 'ogag',
    'pokpok', 'bayaran', 'burikat',
    'bilat', 'pepe', 'puke', 'puki',
    'tite', 'tit3', 'etits', 'burat',
    'tamod', 'tamud', 'jakol', 'kantot',
    'kantutan', 'iyot', 'iyotin', 'tirahin',
    'libog', 'malibog', 'tigang',
    'suso', 'utong',

    'etits', 'ebak',
    'shet', 'fota', 'pota', 'ptngina',
    'tnagina', 'tngina', 'ggss',
    'juts', 'jutski', 'burat', 'ratbu',
    'pekpek', 'kike', 'kepyas',

    'fuck', 'fucking', 'motherfucker',
    'shit', 'bullshit', 'horseshit',
    'bitch', 'bitches',
    'ass', 'asshole', 'jackass',
    'bastard', 'damn', 'goddamn',
    'crap', 'dick', 'cock', 'penis',
    'pussy', 'cunt', 'whore', 'slut',
    'twat', 'wanker', 'jerkoff',
    'retard', 'retarded',
    'idiot', 'moron', 'stupid',
    'dumbass', 'dipshit',
    'nigger', 'nigga',
    'faggot', 'queer',
    'hoe', 'skank',
    'cum', 'semen',
    'blowjob', 'handjob',
    'porn', 'porno',
    'masturbate', 'masturbation',

    'fck', 'fk', 'fucc',
    'sh1t', 'shyt',
    'b1tch', 'biatch',
    'azzhole', 'ashole',
    'phuck', 'fuk',
    'pota', 'potaena',
    'putcha', 'pucha',
    'gag0', 'b0bo',
    'ulul', 'ul0l',
];

    public function containsBadWord(string $text): bool
    {
        $lower = strtolower($text);
        foreach ($this->blockedWords as $word) {
            if (str_contains($lower, strtolower($word))) {
                return true;
            }
        }
        return false;
    }
}