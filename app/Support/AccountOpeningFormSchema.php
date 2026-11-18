<?php

namespace App\Support;

use InvalidArgumentException;

class AccountOpeningFormSchema
{
    public const TYPES = ['individual', 'corporate', 'joint', 'minor', 'estate'];

    private const STEP_TITLES = [
        'Applicant details',
        'Contact & Next of Kin',
        'Bank & Account details',
        'Documents & Declaration',
    ];

    private const FORM_DEFINITIONS = [
        'individual' => [
            'accountLabel' => 'Individual Account',
            'fieldsByStep' => [
                ['surname', 'first_name', 'other_names', 'gender', 'date_of_birth', 'mother_maiden_name', 'occupation', 'nationality', 'state_of_origin', 'lga', 'id_type', 'id_number', 'id_expiry'],
                ['telephone', 'email', 'residential_address', 'contact_address', 'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_address', 'next_of_kin_telephone', 'next_of_kin_email', 'next_of_kin_occupation'],
                ['bank_name', 'bank_branch', 'bank_account_number', 'bank_account_name', 'account_type', 'account_opened_at', 'direct_cash_settlement', 'bvn', 'chn', 'additional_information'],
            ],
            'requiredFieldsByStep' => [
                ['surname', 'first_name', 'date_of_birth', 'nationality', 'id_type', 'id_number', 'id_expiry'],
                ['telephone', 'email', 'residential_address'],
                ['bank_name', 'bank_account_number', 'bank_account_name', 'bvn', 'direct_cash_settlement'],
            ],
            'peopleField' => null,
            'peopleMinimum' => 0,
        ],
        'corporate' => [
            'accountLabel' => 'Corporate Account',
            'fieldsByStep' => [
                ['company_name', 'rc_number', 'date_of_registration', 'nature_of_business', 'business_address', 'directors'],
                ['telephone', 'email', 'contact_address'],
                ['bank_name', 'bank_branch', 'bank_account_number', 'bank_account_name', 'account_type', 'account_opened_at', 'direct_cash_settlement', 'bvn', 'chn', 'additional_information'],
            ],
            'requiredFieldsByStep' => [
                ['company_name', 'rc_number', 'nature_of_business', 'business_address', 'directors'],
                ['telephone', 'email', 'contact_address'],
                ['bank_name', 'bank_account_number', 'bank_account_name', 'bvn'],
            ],
            'peopleField' => 'directors',
            'peopleSingularLabel' => 'Director / Signatory',
            'peopleMinimum' => 1,
        ],
        'joint' => [
            'accountLabel' => 'Joint Account',
            'fieldsByStep' => [
                ['joint_account_name', 'joint_address', 'joint_contact_number', 'direct_cash_settlement'],
                ['joint_applicants'],
                ['bank_name', 'bank_branch', 'bank_account_number', 'bank_account_name', 'account_type', 'account_opened_at', 'bvn', 'chn', 'additional_information'],
            ],
            'requiredFieldsByStep' => [
                ['joint_account_name', 'joint_address', 'joint_contact_number', 'direct_cash_settlement'],
                ['joint_applicants'],
                ['bank_name', 'bank_account_number', 'bank_account_name'],
            ],
            'peopleField' => 'joint_applicants',
            'peopleSingularLabel' => 'Joint Account Holder',
            'peopleMinimum' => 2,
        ],
        'minor' => [
            'accountLabel' => 'Minor Account',
            'fieldsByStep' => [
                ['surname', 'other_names', 'gender', 'date_of_birth', 'residential_address', 'telephone', 'occupation', 'contact_address', 'email', 'nationality', 'state_of_origin', 'lga', 'id_type', 'id_number', 'id_expiry', 'mother_maiden_name'],
                ['guardian_name', 'guardian_relationship', 'guardian_telephone', 'guardian_occupation'],
                ['bank_name', 'bank_branch', 'bank_account_number', 'bank_account_name', 'account_type', 'account_opened_at', 'bvn', 'chn', 'additional_information'],
            ],
            'requiredFieldsByStep' => [
                ['surname', 'date_of_birth', 'nationality', 'id_type', 'id_number', 'id_expiry', 'telephone', 'email', 'residential_address'],
                ['guardian_name', 'guardian_relationship', 'guardian_telephone'],
                ['bank_name', 'bank_account_number', 'bank_account_name'],
            ],
            'peopleField' => null,
            'peopleMinimum' => 0,
        ],
        'estate' => [
            'accountLabel' => 'Estate Account',
            'fieldsByStep' => [
                ['estate_name', 'deceased_date_of_birth', 'mother_maiden_name', 'estate_address'],
                ['administrators'],
                ['bank_name', 'bank_branch', 'bank_account_number', 'bank_account_name', 'account_type', 'account_opened_at', 'direct_cash_settlement', 'bvn', 'chn', 'additional_information'],
            ],
            'requiredFieldsByStep' => [
                ['estate_name', 'deceased_date_of_birth', 'estate_address'],
                ['administrators'],
                ['bank_name', 'bank_account_number', 'bank_account_name'],
            ],
            'peopleField' => 'administrators',
            'peopleSingularLabel' => 'Administrator',
            'peopleMinimum' => 1,
        ],
    ];

    private const FIELD_LABELS = [
        'surname' => 'Surname',
        'first_name' => 'First Name',
        'other_names' => 'Other Names',
        'gender' => 'Gender',
        'date_of_birth' => 'Date of Birth',
        'deceased_date_of_birth' => 'Date of Birth (Deceased)',
        'mother_maiden_name' => 'Mother’s Maiden Name',
        'telephone' => 'Telephone',
        'email' => 'Email Address',
        'occupation' => 'Occupation',
        'nationality' => 'Nationality',
        'state_of_origin' => 'State of Origin',
        'lga' => 'LGA',
        'id_type' => 'Means of Identification',
        'id_number' => 'ID Number (NIN: 11 digits)',
        'id_expiry' => 'ID Expiry Date',
        'residential_address' => 'Residential Address',
        'contact_address' => 'Contact Address',
        'next_of_kin_name' => 'Next of Kin Name',
        'next_of_kin_relationship' => 'Next of Kin Relationship',
        'next_of_kin_telephone' => 'Next of Kin Telephone',
        'next_of_kin_email' => 'Next of Kin Email',
        'next_of_kin_address' => 'Next of Kin Address',
        'next_of_kin_occupation' => 'Next of Kin Occupation',
        'bank_name' => 'Bank Name',
        'bank_branch' => 'Bank Branch',
        'bank_account_number' => 'Bank Account Number (10 digits)',
        'bank_account_name' => 'Bank Account Name',
        'account_type' => 'Account Type',
        'account_opened_at' => 'Date Account Was Opened',
        'direct_cash_settlement' => 'Direct Cash Settlement',
        'bvn' => 'Bank Verification Number (BVN - 11 digits)',
        'chn' => 'Clearing House Number (CHN)',
        'company_name' => 'Company Name',
        'rc_number' => 'RC Number',
        'date_of_registration' => 'Date of Registration',
        'nature_of_business' => 'Nature of Business',
        'business_address' => 'Business Address',
        'joint_account_name' => 'Name of Joint Account',
        'joint_address' => 'Joint Account Address',
        'joint_contact_number' => 'Joint Account Contact Number',
        'minor_name' => 'Minor’s Full Name',
        'guardian_name' => 'Guardian’s Name',
        'guardian_relationship' => 'Relationship to Minor',
        'guardian_telephone' => 'Guardian’s Telephone',
        'guardian_occupation' => 'Guardian’s Occupation',
        'estate_name' => 'Estate Name',
        'estate_address' => 'Estate Address',
        'additional_information' => 'Additional Information / Special Instructions',
        'directors' => 'Company Directors / Signatories',
        'joint_applicants' => 'Joint Account Holders',
        'administrators' => 'Estate Administrators',
    ];

    private const PERSON_FIELD_LABELS = [
        'name' => 'Full Name',
        'residential_address' => 'Residential Address',
        'lga' => 'LGA',
        'city' => 'City',
        'state' => 'State',
        'country' => 'Country',
        'telephone' => 'Mobile Number',
        'email' => 'Email Address',
        'date_of_birth' => 'Date of Birth',
        'nationality' => 'Nationality',
        'id_type' => 'Means of Identification',
        'id_number' => 'ID Number (NIN: 11 digits)',
        'id_expiry' => 'ID Expiry Date',
        'state_of_origin' => 'State of Origin',
        'lga_of_origin' => 'LGA of Origin',
        'occupation' => 'Occupation',
        'next_of_kin_name' => 'Next of Kin Name',
        'next_of_kin_relationship' => 'Next of Kin Relationship',
        'next_of_kin_address' => 'Next of Kin Address',
        'next_of_kin_telephone' => 'Next of Kin Telephone',
        'next_of_kin_email' => 'Next of Kin Email',
        'signature_mandate' => 'Signature Mandate',
    ];

    private const REQUIRED_PERSON_FIELDS = [
        'name',
        'residential_address',
        'telephone',
        'email',
        'date_of_birth',
        'nationality',
        'id_type',
        'id_number',
        'id_expiry',
    ];

    public static function forType(string $type): array
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException("Unsupported account type [{$type}].");
        }

        $definition = self::FORM_DEFINITIONS[$type];
        $definition['type'] = $type;
        $definition['stepTitles'] = self::STEP_TITLES;
        $definition['fieldLabels'] = self::FIELD_LABELS;
        $definition['personFieldLabels'] = self::PERSON_FIELD_LABELS;
        $definition['personFields'] = array_keys(self::PERSON_FIELD_LABELS);
        $definition['requiredPersonFields'] = self::REQUIRED_PERSON_FIELDS;
        $definition['requiredFields'] = array_values(array_unique(
            array_merge(...$definition['requiredFieldsByStep']),
        ));

        return $definition;
    }
}
