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
            $boardFields["board_member_{$i}_image"] = ['', 10240, false, 'image'];
        }

        $boardFields['board_careers_eyebrow'] = ['Careers', 100, false];
        $boardFields['board_careers_title'] = ['Build your future with Heritage.', 255, false];
        $boardFields['board_careers_body'] = ['We value people who bring curiosity, professionalism and a commitment to excellent client service. Contact our team to enquire about career opportunities.', 3000, true];
        $boardFields['board_careers_button'] = ['Contact Us', 100, false];

        $aboutFields = [
            'about_hero_eyebrow' => ['Our Heritage. Your Advantage.', 100, false, 'text', 'Hero'],
            'about_hero_title_line_1' => ['A Legacy of Trust.', 255, false, 'text', 'Hero'],
            'about_hero_title_line_2' => ['A Future of Possibilities.', 255, false, 'text', 'Hero'],
            'about_hero_description' => ['For over three decades, Heritage Capital Markets Limited has been a trusted partner in Nigeria’s capital markets, delivering innovative investment solutions and exceptional value to our clients.', 2000, true, 'text', 'Hero'],
            'about_hero_primary_button' => ['Our Story', 100, false, 'text', 'Hero'],
            'about_hero_secondary_button' => ['Talk to an Expert', 100, false, 'text', 'Hero'],
            'about_hero_side_label_1' => ['People', 100, false, 'text', 'Hero'],
            'about_hero_side_label_2' => ['Expertise', 100, false, 'text', 'Hero'],
            'about_hero_side_label_3' => ['Integrity', 100, false, 'text', 'Hero'],
            'about_hero_side_label_4' => ['Impact', 100, false, 'text', 'Hero'],
            'about_location_name' => ['The Heritage Place', 255, false, 'text', 'Hero'],
            'about_location_city' => ['Lagos, Nigeria', 255, false, 'text', 'Hero'],
            'about_image_hero' => ['', 10240, false, 'image', 'Images'],
            'about_image_hero_alt' => ['Heritage Capital Markets', 255, false, 'text', 'Images'],
            'about_image_reception' => ['', 10240, false, 'image', 'Images'],
            'about_image_reception_alt' => ['The Heritage Place reception', 255, false, 'text', 'Images'],
            'about_image_journey' => ['', 10240, false, 'image', 'Images'],
            'about_image_team' => ['', 10240, false, 'image', 'Images'],
            'about_image_team_alt' => ['Heritage Capital Markets team', 255, false, 'text', 'Images'],
            'about_story_eyebrow' => ['About Heritage', 100, false, 'text', 'Our Story'],
            'about_story_title_line_1' => ['More Than a Firm.', 255, false, 'text', 'Our Story'],
            'about_story_title_line_2' => ['A Lasting Impact.', 255, false, 'text', 'Our Story'],
            'about_story_paragraph_1' => ['Founded with a clear purpose, Heritage Capital Markets Limited has consistently focused on providing reliable, innovative and client-centric investment solutions. We combine deep market knowledge with global best practices to help our clients grow, protect and manage their wealth across all market conditions.', 3000, true, 'text', 'Our Story'],
            'about_story_paragraph_2' => ['Our commitment to integrity, professionalism and excellence continues to drive our reputation as a leading capital markets firm in Nigeria.', 3000, true, 'text', 'Our Story'],
            'about_story_button' => ['Learn More About Our Story', 100, false, 'text', 'Our Story'],
            'about_story_quote' => ['Our success is built on the trust of our clients, our people and the enduring values that guide us.', 1000, true, 'text', 'Our Story'],
            'about_story_quote_byline' => ['Management Team', 255, false, 'text', 'Our Story'],
            'about_stats_title' => ['At a Glance', 255, false, 'text', 'Our Story'],
            'about_image_label' => ['A Professional Environment', 255, false, 'text', 'Our Story'],
            'about_image_label_subtitle' => ['Designed for Your Success', 255, false, 'text', 'Our Story'],
            'about_mission_title' => ['Our Mission', 255, false, 'text', 'Mission, Vision & Values'],
            'about_mission_body' => ['To create sustainable value for our clients through innovative investment solutions, expert guidance and unwavering commitment to excellence.', 2000, true, 'text', 'Mission, Vision & Values'],
            'about_vision_title' => ['Our Vision', 255, false, 'text', 'Mission, Vision & Values'],
            'about_vision_body' => ['To be the most trusted and respected investment firm in Africa, recognized for our integrity, expertise and client-focused solutions.', 2000, true, 'text', 'Mission, Vision & Values'],
            'about_values_title' => ['Our Core Values', 255, false, 'text', 'Mission, Vision & Values'],
            'about_journey_eyebrow' => ['Our Journey', 100, false, 'text', 'Milestones'],
            'about_journey_title' => ['Key Milestones', 255, false, 'text', 'Milestones'],
            'about_journey_description' => ['A journey of growth, innovation and impact in Nigeria’s capital markets.', 1000, true, 'text', 'Milestones'],
            'about_journey_button' => ['Explore Our History', 100, false, 'text', 'Milestones'],
            'about_people_eyebrow' => ['Our People', 100, false, 'text', 'Our People'],
            'about_people_title_line_1' => ['Driven by Expertise.', 255, false, 'text', 'Our People'],
            'about_people_title_line_2' => ['Focused on You.', 255, false, 'text', 'Our People'],
            'about_people_body' => ['Our team of experienced professionals brings deep market knowledge, analytical rigor and a client-focused mindset to every engagement.', 2000, true, 'text', 'Our People'],
            'about_people_button' => ['Meet Our Team', 100, false, 'text', 'Our People'],
            'about_people_image_caption' => ['Collaboration. Insight. Better Outcomes.', 255, false, 'text', 'Our People'],
            'about_careers_title_line_1' => ['Join a Team', 255, false, 'text', 'Our People'],
            'about_careers_title_line_2' => ['That Makes', 255, false, 'text', 'Our People'],
            'about_careers_title_line_3' => ['a Difference.', 255, false, 'text', 'Our People'],
            'about_careers_button' => ['View Careers', 100, false, 'text', 'Our People'],
        ];

        $credibilityItems = [
            ['Established Presence', 'Decades of experience in Nigeria’s capital markets.'],
            ['Client Focused', 'Serving individuals, corporates and institutional clients.'],
            ['Investment Expertise', 'Comprehensive solutions across multiple asset classes.'],
            ['SEC Regulated', 'Licensed and regulated by the Securities and Exchange Commission.'],
        ];

        foreach ($credibilityItems as $index => [$title, $body]) {
            $number = $index + 1;
            $aboutFields["about_credibility_{$number}_title"] = [$title, 255, false, 'text', 'Credibility'];
            $aboutFields["about_credibility_{$number}_body"] = [$body, 1000, true, 'text', 'Credibility'];
        }

        $stats = [
            ['20+', 'Years of', 'Experience'],
            ['100+', 'Clients', 'Served'],
            ['₦50B+', 'Assets Under', 'Management'],
            ['120+', 'Dedicated', 'Professionals'],
        ];

        foreach ($stats as $index => [$value, $label, $label2]) {
            $number = $index + 1;
            $aboutFields["about_stat_{$number}_value"] = [$value, 50, false, 'text', 'Our Story'];
            $aboutFields["about_stat_{$number}_label"] = [$label, 100, false, 'text', 'Our Story'];
            $aboutFields["about_stat_{$number}_label_2"] = [$label2, 100, false, 'text', 'Our Story'];
        }

        foreach (['Integrity', 'Excellence', 'Client Focus', 'Innovation', 'Teamwork'] as $index => $value) {
            $number = $index + 1;
            $aboutFields["about_value_{$number}"] = [$value, 255, false, 'text', 'Mission, Vision & Values'];
        }

        $milestones = [
            ['1990s', 'Our foundation', 'Our foundation and early growth'],
            ['2000s', 'Expanded our', 'Expanded our product offerings'],
            ['2010s', 'Strengthened', 'Strengthened market presence'],
            ['2020s', 'Greater innovation', 'Greater innovation and digital transformation'],
            ['Today', 'A stronger future', 'A stronger future for our clients'],
        ];

        foreach ($milestones as $index => [$year, $title, $body]) {
            $number = $index + 1;
            $aboutFields["about_milestone_{$number}_year"] = [$year, 100, false, 'text', 'Milestones'];
            $aboutFields["about_milestone_{$number}_title"] = [$title, 255, false, 'text', 'Milestones'];
            $aboutFields["about_milestone_{$number}_body"] = [$body, 1000, true, 'text', 'Milestones'];
        }

        $solutionListingFields = [
            'solutions_listing_meta_title' => ['Investment Solutions', 255, false, 'text', 'Search Preview'],
            'solutions_listing_meta_description' => ['Explore Heritage Capital Markets’ investment and capital market solutions, designed around client objectives and investment priorities.', 2000, true, 'text', 'Search Preview'],
            'solutions_listing_hero_eyebrow' => ['Investment Solutions', 100, false, 'text', 'Hero'],
            'solutions_listing_hero_title_line_1' => ['Tailored Solutions', 255, false, 'text', 'Hero'],
            'solutions_listing_hero_title_line_2' => ['for Your', 255, false, 'text', 'Hero'],
            'solutions_listing_hero_title_accent' => ['Financial Goals.', 255, false, 'text', 'Hero'],
            'solutions_listing_hero_description' => ['Access a comprehensive range of investment solutions designed to help you grow, protect and manage your wealth through every stage of your financial journey.', 2000, true, 'text', 'Hero'],
            'solutions_listing_hero_primary_button' => ['Open an Account', 100, false, 'text', 'Hero'],
            'solutions_listing_hero_secondary_button' => ['Talk to an Expert', 100, false, 'text', 'Hero'],
            'solutions_listing_hero_side_label_1' => ['Insights', 100, false, 'text', 'Hero'],
            'solutions_listing_hero_side_label_2' => ['Expertise', 100, false, 'text', 'Hero'],
            'solutions_listing_hero_side_label_3' => ['Opportunity', 100, false, 'text', 'Hero'],
            'solutions_listing_hero_side_label_4' => ['Real Results', 100, false, 'text', 'Hero'],
            'solutions_listing_cards_eyebrow' => ['Our Solutions', 100, false, 'text', 'Solution Cards'],
            'solutions_listing_cards_title' => ['Comprehensive Investment Solutions', 255, false, 'text', 'Solution Cards'],
            'solutions_listing_cards_description' => ['From traditional investments to alternative assets, our solutions are designed to meet the needs of individual, corporate and institutional clients.', 2000, true, 'text', 'Solution Cards'],
            'solutions_listing_journey_eyebrow' => ['Your Investment Journey', 100, false, 'text', 'Investment Journey'],
            'solutions_listing_journey_title_line_1' => ['A Clear Path', 255, false, 'text', 'Investment Journey'],
            'solutions_listing_journey_title_line_2' => ['to Your Goals', 255, false, 'text', 'Investment Journey'],
        ];

        $listingBenefits = [
            ['Client-Centric Approach', 'Your goals are our priority.'],
            ['Risk Managed', 'Disciplined investment process.'],
            ['Data Driven', 'Insights for smarter decisions.'],
            ['Trusted Partner', 'Decades of trust in Nigeria’s capital markets.'],
        ];

        foreach ($listingBenefits as $index => [$title, $body]) {
            $number = $index + 1;
            $solutionListingFields["solutions_listing_benefit_{$number}_title"] = [$title, 255, false, 'text', 'Benefits'];
            $solutionListingFields["solutions_listing_benefit_{$number}_body"] = [$body, 1000, true, 'text', 'Benefits'];
        }

        $listingJourney = [
            ['Understand', 'We learn about your goals and risk appetite.'],
            ['Design', 'We create a tailored investment strategy.'],
            ['Implement', 'We execute with discipline and precision.'],
            ['Grow', 'We monitor and adjust to keep you on track.'],
        ];

        foreach ($listingJourney as $index => [$title, $body]) {
            $number = $index + 1;
            $solutionListingFields["solutions_listing_journey_{$number}_title"] = [$title, 255, false, 'text', 'Investment Journey'];
            $solutionListingFields["solutions_listing_journey_{$number}_body"] = [$body, 1000, true, 'text', 'Investment Journey'];
        }

        $solutionDetailFields = [
            'solution_detail_hero_eyebrow' => ['Investment Solutions', 100, false, 'text', 'Page Sections'],
            'solution_detail_talk_button' => ['Talk to an Expert', 100, false, 'text', 'Page Sections'],
            'solution_detail_all_button' => ['All Solutions', 100, false, 'text', 'Page Sections'],
            'solution_detail_overview_eyebrow' => ['Overview', 100, false, 'text', 'Page Sections'],
            'solution_detail_overview_title' => ['A solution designed around your objectives', 255, false, 'text', 'Page Sections'],
            'solution_detail_why_eyebrow' => ['Why Heritage', 100, false, 'text', 'Page Sections'],
            'solution_detail_why_title' => ['Professional insight. Client focus.', 255, false, 'text', 'Page Sections'],
            'solution_detail_why_body' => ['Our approach combines professional expertise, market insight and a clear understanding of client objectives.', 1500, true, 'text', 'Page Sections'],
            'solution_detail_why_link' => ['Speak with our team', 100, false, 'text', 'Page Sections'],
            'solution_detail_benefits_eyebrow' => ['What You Can Expect', 100, false, 'text', 'Benefits'],
            'solution_detail_benefits_title' => ['Built around your financial priorities', 255, false, 'text', 'Benefits'],
            'solution_detail_journey_eyebrow' => ['Our Approach', 100, false, 'text', 'Approach'],
            'solution_detail_journey_title' => ['A clear path from strategy to action', 255, false, 'text', 'Approach'],
            'solution_detail_related_eyebrow' => ['Explore More', 100, false, 'text', 'Related Solutions'],
            'solution_detail_related_title' => ['Other Investment Solutions', 255, false, 'text', 'Related Solutions'],
            'solution_detail_related_link' => ['View All Solutions', 100, false, 'text', 'Related Solutions'],
        ];

        $solutionBenefits = [
            'portfolio-management' => [
                ['Personalised Strategy', 'Investment strategies aligned with your objectives, risk appetite and investment horizon.'],
                ['Professional Oversight', 'Ongoing professional management and monitoring of your portfolio.'],
                ['Risk Discipline', 'A structured approach to diversification, risk management and portfolio review.'],
            ],
            'investment-advisory' => [
                ['Independent Insight', 'Research-supported perspectives to help you evaluate investment opportunities.'],
                ['Goal Focused', 'Advice structured around your financial objectives and investment priorities.'],
                ['Market Intelligence', 'Professional interpretation of relevant market developments and opportunities.'],
            ],
            'brokerage-dealing' => [
                ['Market Access', 'Professional access to relevant capital market instruments.'],
                ['Efficient Execution', 'Disciplined execution of transactions based on client instructions and objectives.'],
                ['Professional Support', 'A responsive dealing team supporting your market transactions.'],
            ],
            'fixed-income-investments' => [
                ['Income Opportunities', 'Access to suitable fixed income opportunities based on prevailing market conditions.'],
                ['Investment Choice', 'Consideration of different tenures and fixed income instruments.'],
                ['Risk Awareness', 'Clear consideration of tenure, liquidity, yield and relevant risks.'],
            ],
            'alternative-investments' => [
                ['Diversification', 'Explore investment opportunities beyond traditional asset classes.'],
                ['Opportunity Access', 'Identify alternative opportunities suitable for your objectives.'],
                ['Professional Assessment', 'Structured consideration of suitability, risk and investment horizon.'],
            ],
            'financial-planning' => [
                ['Clear Objectives', 'Bring your financial priorities and longer-term goals into focus.'],
                ['Structured Planning', 'Develop a practical framework for managing wealth and investments.'],
                ['Long-Term Perspective', 'Build a plan designed around changing needs and future priorities.'],
            ],
        ];

        foreach ($solutionBenefits as $slug => $benefits) {
            foreach ($benefits as $index => [$title, $body]) {
                $number = $index + 1;
                $solutionDetailFields["solution_detail_{$slug}_benefit_{$number}_title"] = [$title, 255, false, 'text', 'Solution Benefits'];
                $solutionDetailFields["solution_detail_{$slug}_benefit_{$number}_body"] = [$body, 1500, true, 'text', 'Solution Benefits'];
            }
        }

        $detailJourney = [
            ['Understand', 'We understand your objectives, circumstances and risk appetite.'],
            ['Recommend', 'We develop an approach aligned with your investment priorities.'],
            ['Implement', 'We put the agreed strategy or transaction into action.'],
            ['Review', 'We maintain an ongoing focus on your investment objectives.'],
        ];

        foreach ($detailJourney as $index => [$title, $body]) {
            $number = $index + 1;
            $solutionDetailFields["solution_detail_journey_{$number}_title"] = [$title, 255, false, 'text', 'Approach'];
            $solutionDetailFields["solution_detail_journey_{$number}_body"] = [$body, 1000, true, 'text', 'Approach'];
        }

        $investorLoginFields = [
            'investor_meta_title' => ['Investor Login', 255, false, 'text', 'Search Preview'],
            'investor_portal_url' => ['https://heritagecapital.xcloudintegra.com/LoginInnovationbedai.aspx?ReturnUrl=%2f', 500, false, 'url', 'Investor Portal'],
            'investor_hero_eyebrow' => ['Investor Services', 100, false, 'text', 'Page Introduction'],
            'investor_hero_title' => ['Investor Portal', 255, false, 'text', 'Page Introduction'],
            'investor_hero_body' => ['Access your investment account through the Heritage Capital Markets investor portal to view your account and available transaction services.', 2000, true, 'text', 'Page Introduction'],
            'investor_access_eyebrow' => ['Secure Account Access', 100, false, 'text', 'Account Access'],
            'investor_access_title' => ['Your investments, within reach.', 255, false, 'text', 'Account Access'],
            'investor_access_body' => ['Sign in through our investor portal using the credentials provided for your account. Additional identity verification may be required.', 2000, true, 'text', 'Account Access'],
            'investor_access_point_1' => ['Access the existing investor portal.', 500, false, 'text', 'Account Access'],
            'investor_access_point_2' => ['Continue through the provider’s authentication and verification steps.', 500, false, 'text', 'Account Access'],
            'investor_card_eyebrow' => ['Heritage Capital Markets', 255, false, 'text', 'Portal Card'],
            'investor_card_title' => ['Sign in to your account', 255, false, 'text', 'Portal Card'],
            'investor_card_body' => ['Access your account through the Heritage Capital Markets investor portal.', 1500, true, 'text', 'Portal Card'],
            'investor_portal_button' => ['Continue to Investor Portal', 100, false, 'text', 'Portal Card'],
            'investor_portal_notice' => ['You will continue to the external portal to complete sign-in.', 500, false, 'text', 'Portal Card'],
            'investor_help_text' => ['Need assistance accessing your account?', 500, false, 'text', 'Portal Card'],
            'investor_contact_link' => ['Contact Heritage Capital Markets', 100, false, 'text', 'Portal Card'],
        ];

        return [
            'about' => [
                'title' => 'About Us',
                'description' => 'Edit the sections, milestones, statistics, and images shown on the About Us page.',
                'fields' => $aboutFields,
            ],
            'investment-solutions' => [
                'title' => 'Investment Solutions Page',
                'description' => 'Edit the listing page introduction, benefits, solution section, and investment journey. Individual solution cards are managed separately.',
                'fields' => $solutionListingFields,
            ],
            'investment-details' => [
                'title' => 'Investment Solution Details',
                'description' => 'Edit shared detail-page copy and the benefit cards for each investment solution.',
                'fields' => $solutionDetailFields,
            ],
            'investor-login' => [
                'title' => 'Investor Login',
                'description' => 'Edit the portal page copy and external sign-in destination. Authentication remains with the external portal.',
                'fields' => $investorLoginFields,
            ],
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
            if (($field[3] ?? null) === 'image') {
                $rules[$key] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'];
            } elseif (($field[3] ?? null) === 'url') {
                $rules[$key] = ['required', 'string', 'regex:/^https:\/\/.+/i', 'max:' . $field[1]];
            } else {
                $rules[$key] = ['nullable', 'string', 'max:' . $field[1]];
            }
        }

        return $rules;
    }

    private static function groupFieldKeys(string $key): array
    {
        return array_fill_keys(array_keys(self::group($key)['fields']), true);
    }
}