<?php

namespace App\Support;

use InvalidArgumentException;

class AccountOpeningFormSchema
{
    public const TYPES = ['individual', 'corporate', 'joint', 'minor', 'estate'];

    private const STEP_TITLES = [
        'individual' => ['Applicant details', 'Next of Kin', 'Bank details', 'Documents & Declaration'],
        'corporate' => ['Company details', 'Directors / Signatories', 'Bank details', 'Documents & Declaration'],
        'joint' => ['Joint account details', 'Account holders', 'Bank details', 'Documents & Declaration'],
        'minor' => ['Minor details', 'Parent / Guardian', 'Bank details', 'Documents & Declaration'],
        'estate' => ['Estate details', 'Administrators', 'Bank details', 'Documents & Declaration'],
    ];

    private const FORM_DEFINITIONS = [
        'individual' => [
            'accountLabel' => 'Individual Account',
            'fieldsByStep' => [
                ['surname', 'other_names', 'gender', 'date_of_birth', 'residential_address', 'telephone', 'occupation', 'contact_address', 'email', 'nationality', 'state_of_origin', 'lga', 'id_type', 'id_number', 'id_expiry', 'mother_maiden_name'],
                ['next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_telephone', 'next_of_kin_occupation'],
                ['direct_cash_settlement', 'chn', 'bvn', 'bank_name', 'bank_account_number', 'bank_account_name', 'account_type', 'account_opened_at'],
            ],
            'requiredFieldsByStep' => [
                ['surname', 'other_names', 'date_of_birth', 'nationality', 'id_type', 'id_number', 'id_expiry'],
                ['telephone', 'email', 'residential_address'],
                ['bank_name', 'bank_account_number', 'bank_account_name', 'account_type', 'bvn', 'direct_cash_settlement'],
            ],
            'peopleField' => null,
            'peopleMinimum' => 0,
        ],
        'corporate' => [
            'accountLabel' => 'Corporate Account',
            'fieldsByStep' => [
                ['company_name', 'rc_number', 'date_of_registration', 'business_address', 'direct_cash_settlement', 'chn', 'file_number'],
                ['directors'],
                ['bank_name', 'bank_branch', 'bank_account_name', 'bank_account_number', 'account_opened_at', 'account_type', 'bvn'],
            ],
            'requiredFieldsByStep' => [
                ['company_name', 'rc_number', 'business_address', 'directors'],
                [],
                ['bank_name', 'bank_account_number', 'bank_account_name', 'account_type', 'bvn'],
            ],
            'peopleField' => 'directors',
            'peopleSingularLabel' => 'Director / Signatory',
            'peopleMinimum' => 1,
            'peopleMaximum' => 2,
            'personFields' => [
                'name', 'residential_address', 'lga', 'city', 'state', 'country',
                'telephone', 'email', 'date_of_birth', 'state_of_origin', 'lga_of_origin',
                'nationality', 'next_of_kin_name', 'next_of_kin_relationship',
                'next_of_kin_address', 'next_of_kin_telephone', 'next_of_kin_email',
                'signature_mandate',
            ],
        ],
        'joint' => [
            'accountLabel' => 'Joint Account',
            'fieldsByStep' => [
                ['joint_account_name', 'joint_address', 'joint_contact_number', 'direct_cash_settlement', 'chn', 'file_number'],
                ['joint_applicants'],
                ['bank_name', 'bank_branch', 'bank_account_name', 'bank_account_number', 'account_opened_at', 'bvn', 'account_type'],
            ],
            'requiredFieldsByStep' => [
                ['joint_account_name', 'joint_address', 'joint_contact_number', 'direct_cash_settlement'],
                ['joint_applicants'],
                ['bank_name', 'bank_account_number', 'bank_account_name', 'account_type', 'bvn'],
            ],
            'peopleField' => 'joint_applicants',
            'peopleSingularLabel' => 'Joint Account Holder',
            'peopleMinimum' => 2,
            'peopleMaximum' => 2,
            'personFields' => [
                'name', 'residential_address', 'lga', 'city', 'state', 'telephone',
                'email', 'date_of_birth', 'state_of_origin', 'lga_of_origin',
                'nationality', 'next_of_kin_name', 'next_of_kin_relationship',
                'next_of_kin_address', 'next_of_kin_telephone', 'next_of_kin_email',
                'signature_mandate',
            ],
        ],
        'minor' => [
            'accountLabel' => 'Minor Account',
            'fieldsByStep' => [
                ['surname', 'other_names', 'gender', 'date_of_birth', 'residential_address', 'telephone', 'occupation', 'contact_address', 'email', 'nationality', 'state_of_origin', 'lga', 'id_type', 'id_number', 'id_expiry', 'mother_maiden_name'],
                ['guardian_name', 'guardian_relationship', 'guardian_telephone', 'guardian_occupation'],
                ['bank_name', 'bank_branch', 'bank_account_number', 'bank_account_name', 'account_type', 'account_opened_at', 'bvn', 'chn', 'additional_information'],
            ],
            'requiredFieldsByStep' => [
                ['surname', 'other_names', 'date_of_birth', 'nationality', 'id_type', 'id_number', 'id_expiry', 'telephone', 'email', 'residential_address'],
                ['guardian_name', 'guardian_relationship', 'guardian_telephone'],
                ['bank_name', 'bank_account_number', 'bank_account_name', 'account_type', 'bvn'],
            ],
            'peopleField' => null,
            'peopleMinimum' => 0,
        ],
        'estate' => [
            'accountLabel' => 'Estate Account',
            'fieldsByStep' => [
                ['estate_name', 'deceased_date_of_birth', 'mother_maiden_name', 'estate_address', 'direct_cash_settlement', 'chn', 'file_number'],
                ['administrators'],
                ['bank_name', 'bank_branch', 'bank_account_name', 'bank_account_number', 'account_opened_at', 'account_type', 'bvn'],
            ],
            'requiredFieldsByStep' => [
                ['estate_name', 'deceased_date_of_birth', 'estate_address'],
                ['administrators'],
                ['bank_name', 'bank_account_number', 'bank_account_name', 'account_type', 'bvn'],
            ],
            'peopleField' => 'administrators',
            'peopleSingularLabel' => 'Administrator',
            'peopleMinimum' => 1,
            'peopleMaximum' => 2,
            'personFields' => [
                'name', 'residential_address', 'lga', 'city', 'state', 'country',
                'telephone', 'email', 'date_of_birth', 'state_of_origin', 'lga_of_origin',
                'nationality', 'next_of_kin_name', 'next_of_kin_relationship',
                'next_of_kin_address', 'next_of_kin_telephone', 'next_of_kin_email',
                'signature_mandate',
            ],
        ],
    ];

    private const FIELD_LABELS = [
        'surname' => 'Surname',
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
        'id_number' => 'Identification Number',
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
        'file_number' => 'File Number',
        'company_name' => 'Company Name',
        'rc_number' => 'RC Number',
        'date_of_registration' => 'Date of Registration',
        'business_address' => 'Business Address',
        'joint_account_name' => 'Name of Joint Account',
        'joint_address' => 'Joint Account Address',
        'joint_contact_number' => 'Joint Account Contact Number',
        'guardian_name' => 'Guardian’s Name',
        'guardian_relationship' => 'Relationship to Minor',
        'guardian_telephone' => 'Guardian’s Telephone',
        'guardian_occupation' => 'Guardian’s Occupation',
        'estate_name' => 'Estate Name',
        'estate_address' => 'Estate Address',
        'additional_information' => 'Additional Information / Special Instructions',
        'indemnity_date' => 'E-Indemnity Date',
        'indemnity_witness_name' => 'E-Indemnity Witness Name',
        'indemnity_witness_signature' => 'E-Indemnity Witness Signature',
        'indemnity_witness_date' => 'E-Indemnity Witness Date',
        'legal_signatories' => 'Legal Disclosure Signatories',
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
        'state_of_origin' => 'State of Origin',
        'lga_of_origin' => 'LGA of Origin',
        'occupation' => 'Occupation',
        'next_of_kin_name' => 'Next of Kin Name',
        'next_of_kin_relationship' => 'Next of Kin Relationship',
        'next_of_kin_address' => 'Next of Kin Address',
        'next_of_kin_telephone' => 'Next of Kin Telephone',
        'next_of_kin_email' => 'Next of Kin Email',
        'signature_mandate' => 'Signature Mandate',
        'terms_name' => 'Name for Terms and Conditions',
        'terms_signature' => 'Terms and Conditions Signature',
        'terms_date' => 'Terms and Conditions Date',
        'aml_name' => 'Name for Anti-Money Laundering Declaration',
        'aml_signature' => 'Anti-Money Laundering Signature',
        'aml_date' => 'Anti-Money Laundering Date',
        'indemnity_name' => 'Name for E-Indemnity',
        'indemnity_signature' => 'E-Indemnity Signature',
    ];

    private const REQUIRED_PERSON_FIELDS = [
        'name',
        'residential_address',
        'telephone',
        'email',
        'date_of_birth',
        'nationality',
    ];

    private const IDENTIFICATION_TYPES = [
        'NIN',
        'National ID Card',
        'International Passport',
        "Driver's License",
        "Voter's Card",
        'Other',
    ];

    public static function forType(string $type): array
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException("Unsupported account type [{$type}].");
        }

        $definition = self::FORM_DEFINITIONS[$type];
        $definition['type'] = $type;
        $definition['stepTitles'] = self::STEP_TITLES[$type];
        $definition['fieldLabels'] = self::FIELD_LABELS;
        $definition['personFieldLabels'] = self::PERSON_FIELD_LABELS;
        $definition['personFields'] = $definition['personFields'] ?? [];
        $definition['peopleMaximum'] = $definition['peopleMaximum'] ?? 20;
        $definition['requiredPersonFields'] = $definition['peopleField'] === null
            ? []
            : self::REQUIRED_PERSON_FIELDS;
        $definition['identificationTypes'] = self::IDENTIFICATION_TYPES;
        $definition['legalDisclosure'] = self::legalDisclosure($type);
        $definition['legalSignatoryLabel'] = match ($type) {
            'individual' => 'Applicant',
            'minor' => 'Parent / Guardian',
            'corporate' => 'Director / Signatory',
            'joint' => 'Joint Account Holder',
            'estate' => 'Administrator',
        };
        $definition['requiredFields'] = array_values(array_unique(
            array_merge(...$definition['requiredFieldsByStep']),
        ));

        return $definition;
    }

    private static function legalDisclosure(string $type): array
    {
        $terms = [
            'a' => 'HCML offices are open between the hours of 8 a.m. and 5 p.m. on each day that is designated a Business day in Nigeria (“Business Day”).',
            'b' => 'Client advices and mandates should be sent physically to HCML office in Lagos or other office, as HCML may from time to time maintain elsewhere in Nigeria, or via e-mail and SMS. The dedicated e-mail address for mandates is mandates@heritagecapitals.com.ng, info@heritagecapitals.com.ng, and the number for SMS mandate is 07013113933.',
            'c' => 'The daily transaction deadline for the receipt of stock mandates and/or amendments from clients is 10 a.m. (Nigerian Time) of the Business Day on which such Mandate is to be executed.',
            'd' => 'Where a mandate consists of a purchase instruction, it can only be processed if sufficient cleared funds are available to execute such an instruction and prevailing market conditions.',
            'e' => 'Valid mandates have an execution period of five (5) Business Days unless otherwise specified by the client, after which they will be deemed to have expired.',
        ];

        if (in_array($type, ['individual', 'minor'], true)) {
            $terms['f'] = 'The client is liable to pay all applicable statutory charges on transactions executed on the floors of various Stock Exchanges as may be advised from time to time by the regulatory authorities. Please confirm the ruling charges from the Customer Service Officers or through the Customer Service listed elsewhere on this form.';
            $terms['g'] = 'All other negotiated transactions consummated outside the floors of the Stock Exchanges shall attract charges as will be specifically agreed between the parties in writing.';
        } else {
            $terms['f'] = 'Clients can either choose to be settled directly by CSCS or paid by Heritage Capital Markets Limited after a transaction is consummated. Clients who choose to settle directly should indicate by filling a direct settlement mandate form and submit it to us for processing.';
            $terms['g'] = 'The client is liable to pay all applicable statutory charges on transactions executed on the floors of various Stock Exchanges as may be advised from time to time by the regulatory authorities. Please confirm the ruling charges from the Customer Service Officers or through the Customer Service listed elsewhere on this form.';
            $terms['h'] = 'All other negotiated transactions consummated outside the floors of the Stock Exchanges shall attract charges as will be specifically agreed between the parties in writing.';
        }

        return [
            'termsConditions' => [
                'title' => 'Terms and Conditions',
                'clauses' => $terms,
                'acknowledgement' => 'I/We, the undersigned, have read and are in agreement with the Terms & Conditions and the Risk Disclosures above.',
            ],
            'antiMoneyLaundering' => [
                'title' => 'Anti-Money Laundering Act',
                'paragraphs' => [
                    'To help the Federal Government of Nigeria fight the funding of terrorism and money laundering activities, the Money Laundering (Prohibition) Act 2013 requires all financial and non-financial institutions to obtain, verify and record information that identifies each person who opens an account. In this regard, when you open an account with us at HCML, we will ask for your name, address, means of identification, occupation and other information that will enable us to identify you and verify your identity on an ongoing basis.',
                    'Furthermore, in compliance with the Money Laundering (Prohibition) Act 2013, any single transaction, lodgement or transfer of funds in excess of ₦5,000,000.00 or its equivalent in the case of an individual, or ₦10,000,000.00 or its equivalent in the case of a corporate body, shall be reported in writing within seven (7) days to the Nigerian Financial Intelligence Unit (NFIU) and the functions of the Securities & Exchange Commission (as applicable).',
                ],
            ],
            'eIndemnity' => [
                'title' => 'E-Indemnity',
                'paragraphs' => [
                    'Whereas I/We, whose name(s), email address(es) and telephone number(s) are provided in this application, hereby declare that I/We are fully aware that Buy and/or Sell Mandates for the trade of shares/stocks/bonds through our CENTRAL SECURITIES CLEARING SYSTEM LTD (CSCS) Account domiciled with the company shall be by Buy and/or Sell Mandate Form executed in accordance with the service offering subscribed to, and such instructions must emanate from the email address and phone number(s) filled in this account opening form or by a hard copy of a duly executed mandate form by authorized signatories.',
                    'I/We understand that the company is not required to verify the identity of the person giving instructions in my/our name provided the instructions are in line with our records with the Company.',
                    'I/We understand that it is my/our responsibility to keep my/our communication details (Username, Password) and phone device private and confidential to prevent unauthorized access, and we will immediately notify the company should we detect a breach of these medium.',
                    'The company has requested and I/We have agreed to provide the indemnity under the conditions herein contained.',
                    'NOW THEREFORE, I/We instruct that the company should accept and execute instructions and/or give effects to requests to buy or sell stocks on my/our behalf, any other instruction relating to my account on any of the services usually rendered by the company to her clients, where such instructions and/or requests are given by the aforementioned means.',
                    'IN CONSIDERATION of the company agreeing to accept and act upon any such instructions, communications and documents by telephone, SMS, e-mail letters issued by me for sales of shares/stocks/bonds and other forms of unsecured means of communications and unaccompanied by a duly executed Buy/Sell Mandate Form, we hereby irrevocably undertake to indemnify the company and hold it harmless from and against all costs (including without limitation to legal fees and expenses, claims, losses, liabilities, damages and proceedings) whatsoever that the company may incur or that may arise as a result of the Company accepting or acting upon such instructions, communication or documents and including risks due to errors in transaction, misunderstanding or error on the part of the company regarding my identity.',
                    'I/We hereby irrevocably release the Company from all liabilities in the event any telephone, text messages, e-mail or letter is not received, or is mutilated, altered, illegible or interrupted, or duplicated, incomplete, unauthorized, or delayed for any reason.',
                    'The company shall have absolute discretion, for any reason whatsoever, to act or not to act upon documentation received by facsimile, e-mail or letters or instructions received by telephone unaccompanied by my executed Buy and/or Sell Mandate Form and/or to request verification of documents and instructions received by such means.',
                    'This Indemnity shall be governed by and construed in accordance with the laws of the Federal Republic of Nigeria.',
                ],
            ],
            'riskDisclosure' => [
                'title' => 'Risk Disclosure Statement',
                'paragraphs' => [
                    'As a client of Heritage Capital Markets Limited, you must acknowledge and accept that there can be no guarantee of profits or exemption from losses while executing orders for the purchase and/or sale of SECURITIES being traded on the floor of the Nigerian Stock Exchange.',
                    'It is vital for you to understand that the inherent nature of trading securities entails significant risks, and past performance cannot assure future outcomes. The value of investments may fluctuate, resulting in potential financial losses, including the loss of principal invested. Therefore, before engaging in any trading activities, you should meticulously evaluate your investment objectives, risk tolerance, and financial circumstances.',
                    'Heritage Capital Markets Limited advises all clients to exercise prudence and seek guidance to ensure informed decision-making regarding investment strategies and risk management. Our company is committed to providing support and guidance to clients to facilitate informed investment decisions.',
                    'Heritage Capital Markets Limited shall not be liable for any losses incurred during trading activities on the Exchange.',
                ],
            ],
        ];
    }
}
