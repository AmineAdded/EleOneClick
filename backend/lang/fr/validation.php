<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

        'required' => 'Le champ :attribute est obligatoire.',
        'email' => "Le champ :attribute doit être une adresse email valide.",
        'min' => [
            'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        ],
        'accepted' => 'Le champ :attribute doit être accepté.',
        'accepted_if' => 'Le champ :attribute doit être accepté lorsque :other est :value.',
        'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
        'after' => 'Le champ :attribute doit être une date postérieure à :date.',
        'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale à :date.',
        'alpha' => 'Le champ :attribute ne doit contenir que des lettres.',
        'alpha_dash' => 'Le champ :attribute ne doit contenir que des lettres, chiffres, tirets et underscores.',
        'alpha_num' => 'Le champ :attribute ne doit contenir que des lettres et des chiffres.',
        'array' => 'Le champ :attribute doit être un tableau.',
        'ascii' => 'Le champ :attribute ne doit contenir que des caractères ASCII.',
        'before' => 'Le champ :attribute doit être une date antérieure à :date.',
        'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale à :date.',
        'between' => [
            'array' => 'Le tableau :attribute doit avoir entre :min et :max éléments.',
            'file' => 'Le fichier :attribute doit avoir une taille entre :min et :max kilo-octets.',
            'numeric' => 'La valeur de :attribute doit être entre :min et :max.',
            'string' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
        ],
        'boolean' => 'Le champ :attribute doit être vrai ou faux.',
        'confirmed' => 'La confirmation de :attribute ne correspond pas.',
        'current_password' => 'Le mot de passe est incorrect.',
        'date' => 'Le champ :attribute n\'est pas une date valide.',
        'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
        'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
        'decimal' => 'Le champ :attribute doit contenir :decimal décimales.',
        'declined' => 'Le champ :attribute doit être refusé.',
        'declined_if' => 'Le champ :attribute doit être refusé lorsque :other est :value.',
        'different' => 'Les champs :attribute et :other doivent être différents.',
        'digits' => 'Le champ :attribute doit contenir :digits chiffres.',
        'digits_between' => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
        'dimensions' => 'Les dimensions de l\'image :attribute sont invalides.',
        'distinct' => 'Le champ :attribute a une valeur en double.',
        'doesnt_end_with' => 'Le champ :attribute ne doit pas se terminer par :values.',
        'doesnt_start_with' => 'Le champ :attribute ne doit pas commencer par :values.',
        'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
        'ends_with' => 'Le champ :attribute doit se terminer par l\'un des éléments suivants : :values.',
        'enum' => 'La sélection du champ :attribute est invalide.',
        'exists' => 'La sélection du champ :attribute est invalide.',
        'file' => 'Le champ :attribute doit être un fichier.',
        'filled' => 'Le champ :attribute doit avoir une valeur.',
        'gt' => [
            'array' => 'Le champ :attribute doit avoir plus de :value éléments.',
            'file' => 'Le fichier :attribute doit être supérieur à :value kilo-octets.',
            'numeric' => 'La valeur de :attribute doit être supérieure à :value.',
            'string' => 'Le champ :attribute doit contenir plus de :value caractères.',
        ],
        'gte' => [
            'array' => 'Le champ :attribute doit avoir au moins :value éléments.',
            'file' => 'Le fichier :attribute doit être supérieur ou égal à :value kilo-octets.',
            'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :value.',
            'string' => 'Le champ :attribute doit contenir au moins :value caractères.',
        ],
        'image' => 'Le champ :attribute doit être une image.',
        'in' => 'La sélection du champ :attribute est invalide.',
        'in_array' => 'Le champ :attribute n\'existe pas dans :other.',
        'integer' => 'Le champ :attribute doit être un entier.',
        'ip' => 'Le champ :attribute doit être une adresse IP valide.',
        'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
        'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
        'json' => 'Le champ :attribute doit être une chaîne JSON valide.',
        'lowercase' => 'Le champ :attribute doit être en minuscules.',
        'lt' => [
            'array' => 'Le champ :attribute doit avoir moins de :value éléments.',
            'file' => 'Le fichier :attribute doit être inférieur à :value kilo-octets.',
            'numeric' => 'La valeur de :attribute doit être inférieure à :value.',
            'string' => 'Le champ :attribute doit contenir moins de :value caractères.',
        ],
        'lte' => [
            'array' => 'Le champ :attribute ne doit pas avoir plus de :value éléments.',
            'file' => 'Le fichier :attribute doit être inférieur ou égal à :value kilo-octets.',
            'numeric' => 'La valeur de :attribute doit être inférieure ou égale à :value.',
            'string' => 'Le champ :attribute doit contenir au maximum :value caractères.',
        ],
        'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
        'max' => [
            'array' => 'Le champ :attribute ne peut pas avoir plus de :max éléments.',
            'file' => 'Le fichier :attribute ne peut pas être supérieur à :max kilo-octets.',
            'numeric' => 'La valeur de :attribute ne peut pas dépasser :max.',
            'string' => 'Le champ :attribute ne peut pas contenir plus de :max caractères.',
        ],
        'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
        'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
        'min' => [
            'array' => 'Le champ :attribute doit avoir au moins :min éléments.',
            'file' => 'Le fichier :attribute doit avoir au moins :min kilo-octets.',
            'numeric' => 'La valeur de :attribute doit être au moins de :min.',
            'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        ],
        // Ajoutez ici les autres traductions si nécessaire...

    

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
