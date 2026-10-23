<?php

namespace App\Support;

use App\Models\SiteSetting;

class PublicPageContent
{
    public static function fields(): array
    {
        $boardFields = [
            'board_eyebrow' => ['Our People', 100, false],
            'board_title' => ['Board & Management', 255, false],
            'board_intro' => ['The people of Heritage bring together experience, market knowledge and a commitment to serving our clients.', 2000, true],
            'board_leadership_eyebrow' => ['Leadership', 100, false],
            'board_leadership_title' => ['Experience guided by integrity.', 255, false],
            'board_leadership_body' => ['Our leadership is committed to responsible oversight, professional standards and a client-focused approach. We bring together people with the expertise to help clients navigate the capital markets.', 3000, true],
            'board_profiles_note' => ['Information about individual board and management members will be published here as approved profiles become available.', 2000, true],
        ];

        // Dynamically generate fields for 12 board members
        for ($i = 1; $i <= 12; $i++) {
            $boardFields["board_member_{$i}_name"] = ['', 255, false];
            $boardFields["board_member_{$i}_role"] = ['', 255, false];
            $boardFields["board_member_{$i}_bio"] = ['', 50000, true];
            $boardFields["board_member_{$i}_image"] = ['', 5120, false, 'image'];
        }

        $boardFields['board_careers_eyebrow'] = ['Careers', 100, false];
        $boardFields['board_careers_title'] = ['Build your future with Heritage.', 255, false];
        $boardFields['board_careers_body'] = ['We value people who bring curiosity, professionalism and a commitment to excellent client service. Contact our team to enquire about career opportunities.', 3000, true];
        $boardFields['board_careers_button'] = ['Contact Us', 100, false];

        return [
            'board' => [
                'title' => 'Board & Management',
                'description' => 'Add member names and details below. Leave a name blank to keep that profile hidden.',
                'fields' => $boardFields,
            ],
            'governance' => [
                'title' => 'Corporate Governance',
                'fields' => [
                    'governance_eyebrow' => ['About Heritage', 100, false],
                    'governance_title' => ['Corporate Governance', 255, false],
                    'governance_intro' => ['Our approach is grounded in integrity, accountability and responsible service to our clients.', 2000, true],
                    'governance_principles_title' => ['Our principles', 255, false],
                    'governance_principles_intro' => ['Heritage Capital Markets is committed to conducting its business with professionalism and transparency. We aim to act in our clients’ interests, uphold applicable requirements and maintain high standards in our work.', 3000, true],
                    'governance_principle_1_title' => ['Integrity', 255, false],
                    'governance_principle_1_body' => ['We value honest conduct and clear communication.', 1000, true],
                    'governance_principle_2_title' => ['Accountability', 255, false],
                    'governance_principle_2_body' => ['We take responsibility for our decisions and actions.', 1000, true],
                    'governance_principle_3_title' => ['Client focus', 255, false],
                    'governance_principle_3_body' => ['We work to understand and support our clients’ needs.', 1000, true],
                    'governance_contact_text' => ['For questions about our governance practices, please contact us.', 500, false],
                ],
            ],
            'fees' => [
                'title' => 'Fees & Charges',
                'fields' => [
                    'fees_eyebrow' => ['Investment Solutions', 100, false],
                    'fees_title' => ['Fees & Charges', 255, false],
                    'fees_intro' => ['Get clear information about the costs associated with our services.', 2000, true],
                    'fees_section_title' => ['Request current fee information', 255, false],
                    'fees_section_body' => ['Fees and charges may depend on the service, product and transaction. Contact our team for the current fee schedule and details relevant to your needs before proceeding.', 3000, true],
                    'fees_contact_button' => ['Contact Us', 100, false],
                ],
            ],
            'resources' => [
                'title' => 'Investor Resources',
                'fields' => [
                    'resources_title' => ['Investor Resources', 255, false],
                    'resources_intro' => ['Market information and learning resources to help you build your understanding of investing.', 2000, true],
                    'resources_market_eyebrow' => ['Market Data', 100, false],
                    'resources_market_title' => ['Market indices', 255, false],
                    'resources_market_description' => ['The latest market values maintained by Heritage Capital Markets. Figures may be delayed; check the date shown and confirm current information before making investment decisions.', 2000, true],
                    'resources_education_eyebrow' => ['Investor Education', 100, false],
                    'resources_education_title' => ['Build your investment knowledge', 255, false],
                    'resources_topic_1_title' => ['Understanding investment risk', 255, false],
                    'resources_topic_1_body' => ['Consider how market movements and the possibility of loss relate to your objectives and investment timeframe.', 1500, true],
                    'resources_topic_2_title' => ['Diversification', 255, false],
                    'resources_topic_2_body' => ['Learn how spreading investments across different assets may help manage concentration risk.', 1500, true],
                    'resources_topic_3_title' => ['Setting investment goals', 255, false],
                    'resources_topic_3_body' => ['Define your objectives, timeframe and liquidity needs before choosing an investment approach.', 1500, true],
                    'resources_education_disclaimer' => ['This information is general education, not personal investment advice. Consider your circumstances and seek professional advice where appropriate.', 1500, true],
                    'resources_glossary_eyebrow' => ['Glossary', 100, false],
                    'resources_glossary_title' => ['Common investment terms', 255, false],
                    'resources_glossary_1_term' => ['Asset', 255, false],
                    'resources_glossary_1_definition' => ['An item of value that may be owned or invested in.', 1000, true],
                    'resources_glossary_2_term' => ['Diversification', 255, false],
                    'resources_glossary_2_definition' => ['Spreading investments across different assets or markets.', 1000, true],
                    'resources_glossary_3_term' => ['Equity', 255, false],
                    'resources_glossary_3_definition' => ['An ownership interest in a company, commonly represented by shares.', 1000, true],
                    'resources_glossary_4_term' => ['Fixed income', 255, false],
                    'resources_glossary_4_definition' => ['An investment that generally pays interest under stated terms, such as a bond.', 1000, true],
                    'resources_glossary_5_term' => ['Market index', 255, false],
                    'resources_glossary_5_definition' => ['A measure that tracks the performance of a selected group of securities.', 1000, true],
                    'resources_glossary_6_term' => ['Portfolio', 255, false],
                    'resources_glossary_6_definition' => ['A collection of investments held by an individual or institution.', 1000, true],
                ],
            ],
            'privacy' => [
                'title' => 'Privacy Policy',
                'description' => 'Have this draft reviewed and completed against the company’s actual privacy practices before publication.',
                'fields' => [
                    'privacy_title' => ['Privacy Policy', 255, false],
                    'privacy_intro' => ['This policy is being prepared. Please contact Heritage Capital Markets with privacy questions while the approved policy is being finalized.', 3000, true],
                    'privacy_updated_at' => ['', 100, false],
                    'privacy_section_1_title' => ['Information we collect', 255, false],
                    'privacy_section_1_body' => ['', 10000, true],
                    'privacy_section_2_title' => ['How we use information', 255, false],
                    'privacy_section_2_body' => ['', 10000, true],
                    'privacy_section_3_title' => ['Sharing and disclosure', 255, false],
                    'privacy_section_3_body' => ['', 10000, true],
                    'privacy_section_4_title' => ['Security and retention', 255, false],
                    'privacy_section_4_body' => ['', 10000, true],
                    'privacy_section_5_title' => ['Your rights and choices', 255, false],
                    'privacy_section_5_body' => ['', 10000, true],
                    'privacy_section_6_title' => ['Contact us', 255, false],
                    'privacy_section_6_body' => ['', 3000, true],
                ],
            ],
            'terms' => [
                'title' => 'Terms of Use',
                'description' => 'Have this draft reviewed by appropriate legal counsel and ensure it reflects the intended website terms before publication.',
                'fields' => [
                    'terms_title' => ['Terms of Use', 255, false],
                    'terms_intro' => ['These terms are being prepared. Please contact Heritage Capital Markets with questions while the approved terms are being finalized.', 3000, true],
                    'terms_updated_at' => ['', 100, false],
                    'terms_section_1_title' => ['Acceptance of terms', 255, false],
                    'terms_section_1_body' => ['', 10000, true],
                    'terms_section_2_title' => ['Website content and information', 255, false],
                    'terms_section_2_body' => ['', 10000, true],
                    'terms_section_3_title' => ['Intellectual property', 255, false],
                    'terms_section_3_body' => ['', 10000, true],
                    'terms_section_4_title' => ['Acceptable use', 255, false],
                    'terms_section_4_body' => ['', 10000, true],
                    'terms_section_5_title' => ['Third-party links and liability', 255, false],
                    'terms_section_5_body' => ['', 10000, true],
                    'terms_section_6_title' => ['Contact us', 255, false],
                    'terms_section_6_body' => ['', 3000, true],
                ],
            ],
            'risk' => [
                'title' => 'Risk Disclosure',
                'description' => 'Have this draft reviewed by compliance and legal advisers to ensure it accurately reflects the company’s products and regulatory obligations.',
                'fields' => [
                    'risk_title' => ['Risk Disclosure', 255, false],
                    'risk_intro' => ['This disclosure is being prepared. Please contact Heritage Capital Markets for relevant product information before making investment decisions.', 3000, true],
                    'risk_updated_at' => ['', 100, false],
                    'risk_section_1_title' => ['Investment risk', 255, false],
                    'risk_section_1_body' => ['', 10000, true],
                    'risk_section_2_title' => ['Suitability and objectives', 255, false],
                    'risk_section_2_body' => ['', 10000, true],
                    'risk_section_3_title' => ['Past performance and forecasts', 255, false],
                    'risk_section_3_body' => ['', 10000, true],
                    'risk_section_4_title' => ['Information and advice', 255, false],
                    'risk_section_4_body' => ['', 10000, true],
                    'risk_section_5_title' => ['Product-specific risks', 255, false],
                    'risk_section_5_body' => ['', 10000, true],
                    'risk_section_6_title' => ['Contact us', 255, false],
                    'risk_section_6_body' => ['', 3000, true],
                ],
            ],
        ];
    }

    public static function defaults(): array
    {
        $defaults = [];

        foreach (self::fields() as $group) {
            foreach ($group['fields'] as $key => [$value]) {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    public static function group(string $key): array
    {
        $groups = self::fields();
        abort_unless(isset($groups[$key]), 404);

        return $groups[$key];
    }

    public static function groupDefaults(string $key): array
    {
        return array_intersect_key(
            self::defaults(),
            self::groupFieldKeys($key)
        );
    }

    public static function groupValues(string $key): array
    {
        return array_intersect_key(self::values(), self::groupFieldKeys($key));
    }

    public static function values(): array
    {
        $defaults = self::defaults();
        $stored = SiteSetting::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->all();

        return array_replace($defaults, $stored);
    }

    public static function validationRules(string $groupKey): array
    {
        $rules = [];
        $group = self::group($groupKey);

        foreach ($group['fields'] as $key => $field) {
            $rules[$key] = ($field[3] ?? null) === 'image'
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']
                : ['nullable', 'string', 'max:' . $field[1]];
        }

        return $rules;
    }

    private static function groupFieldKeys(string $key): array
    {
        return array_fill_keys(array_keys(self::group($key)['fields']), true);
    }
}