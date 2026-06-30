<?php

return [
    'accepted' => 'Le champ :attribute doit être accepté.',
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
    ],
    'max' => [
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'file' => 'Le fichier :attribute ne peut pas dépasser :max kilo-octets.',
    ],
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'unique' => 'Cette valeur de :attribute est déjà utilisée.',
    'exists' => 'La valeur sélectionnée pour :attribute est invalide.',
    'image' => 'Le champ :attribute doit être une image.',
    'uploaded' => 'Le téléversement de :attribute a échoué. Vérifiez la taille (max. 2 Mo) et le format (JPG, PNG).',
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'integer' => 'Le champ :attribute doit être un entier.',
    'digits' => 'Le champ :attribute doit contenir :digits chiffres.',
    'attributes' => [
        'name' => 'nom',
        'email' => 'email',
        'password' => 'mot de passe',
        'phone' => 'téléphone',
        'category_id' => 'catégorie',
        'description' => 'description',
        'price' => 'prix',
        'stock' => 'stock',
        'image' => 'image',
        'shipping_address' => 'adresse de livraison',
        'shipping_phone' => 'téléphone de livraison',
        'code' => 'code OTP',
    ],
];
