<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for edit forms and responses.
 */
class EditIndividualController extends AbstractEditController
{
    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function reorderMedia(Request $request, Tree $tree): Response
    {
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $title = $individual->fullName() . ' — ' . I18N::translate('Re-order media');

        return $this->viewResponse('edit/reorder-media', [
            'title'      => $title,
            'individual' => $individual,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function reorderMediaAction(Request $request, Tree $tree): Response
    {
        $xref       = $request->get('xref', '');
        $order      = (array) $request->get('order', []);
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $dummy_facts = ['0 @' . $individual->xref() . '@ INDI'];
        $sort_facts  = [];
        $keep_facts  = [];

        // Split facts into OBJE and other
        foreach ($individual->facts() as $fact) {
            if ($fact->getTag() === 'OBJE') {
                $sort_facts[$fact->id()] = $fact->gedcom();
            } else {
                $keep_facts[] = $fact->gedcom();
            }
        }

        // Sort the facts
        uksort($sort_facts, static function ($x, $y) use ($order) {
            return array_search($x, $order, true) - array_search($y, $order, true);
        });

        // Merge the facts
        $gedcom = implode("\n", array_merge($dummy_facts, $sort_facts, $keep_facts));

        $individual->updateRecord($gedcom, false);

        return new RedirectResponse($individual->url());
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function reorderNames(Request $request, Tree $tree): Response
    {
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $title = $individual->fullName() . ' — ' . I18N::translate('Re-order names');

        return $this->viewResponse('edit/reorder-names', [
            'title'      => $title,
            'individual' => $individual,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function reorderNamesAction(Request $request, Tree $tree): Response
    {
        $xref       = $request->get('xref', '');
        $order      = (array) $request->get('order', []);
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $dummy_facts = ['0 @' . $individual->xref() . '@ INDI'];
        $sort_facts  = [];
        $keep_facts  = [];

        // Split facts into NAME and other
        foreach ($individual->facts() as $fact) {
            if ($fact->getTag() === 'NAME') {
                $sort_facts[$fact->id()] = $fact->gedcom();
            } else {
                $keep_facts[] = $fact->gedcom();
            }
        }

        // Sort the facts
        uksort($sort_facts, static function ($x, $y) use ($order) {
            return array_search($x, $order, true) - array_search($y, $order, true);
        });

        // Merge the facts
        $gedcom = implode("\n", array_merge($dummy_facts, $sort_facts, $keep_facts));

        $individual->updateRecord($gedcom, false);

        return new RedirectResponse($individual->url());
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function reorderSpouses(Request $request, Tree $tree): Response
    {
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $title = $individual->fullName() . ' — ' . I18N::translate('Re-order families');

        return $this->viewResponse('edit/reorder-spouses', [
            'title'      => $title,
            'individual' => $individual,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function reorderSpousesAction(Request $request, Tree $tree): Response
    {
        $xref       = $request->get('xref', '');
        $order      = (array) $request->get('order', []);
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $dummy_facts = ['0 @' . $individual->xref() . '@ INDI'];
        $sort_facts  = [];
        $keep_facts  = [];

        // Split facts into FAMS and other
        foreach ($individual->facts() as $fact) {
            if ($fact->getTag() === 'FAMS') {
                $sort_facts[$fact->id()] = $fact->gedcom();
            } else {
                $keep_facts[] = $fact->gedcom();
            }
        }

        // Sort the facts
        uksort($sort_facts, static function ($x, $y) use ($order) {
            return array_search($x, $order, true) - array_search($y, $order, true);
        });

        // Merge the facts
        $gedcom = implode("\n", array_merge($dummy_facts, $sort_facts, $keep_facts));

        $individual->updateRecord($gedcom, false);

        return new RedirectResponse($individual->url());
    }

    /**
     * Add a child to an existing individual (creating a one-parent family).
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function addChild(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $title = $individual->fullName() . ' - ' . I18N::translate('Add a child to create a one-parent family');

        return $this->viewResponse('edit/new-individual', [
            'tree'       => $tree,
            'title'      => $title,
            'nextaction' => 'add_child_to_individual_action',
            'individual' => $individual,
            'family'     => null,
            'name_fact'  => null,
            'famtag'     => 'CHIL',
            'gender'     => 'U',
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function addChildAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $PEDI = $request->get('PEDI', '');

        $this->glevels = $request->get('glevels', []);
        $this->tag     = $request->get('tag', []);
        $this->text    = $request->get('text', []);
        $this->islink  = $request->get('islink', []);


        // Create a family
        if ($individual->sex() === 'F') {
            $gedcom = "0 @@ FAM\n1 WIFE @" . $individual->xref() . '@';
        } else {
            $gedcom = "0 @@ FAM\n1 HUSB @" . $individual->xref() . '@';
        }
        $family = $tree->createFamily($gedcom);

        // Link the parent to the family
        $individual->createFact('1 FAMS @' . $family->xref() . '@', true);

        // Create a child
        $this->splitSource(); // separate SOUR record from the rest

        $gedcom = '0 @@ INDI';
        $gedcom .= $this->addNewName($request, $tree);
        $gedcom .= $this->addNewSex($request);
        $gedcom .= "\n" . GedcomCodePedi::createNewFamcPedi($PEDI, $family->xref());
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedcom .= $this->addNewFact($request, $tree, $match);
            }
        }
        if ((bool) $request->get('SOUR_INDI')) {
            $gedcom = $this->handleUpdates($gedcom);
        } else {
            $gedcom = $this->updateRest($gedcom);
        }

        $child = $tree->createIndividual($gedcom);

        // Link the family to the child
        $family->createFact('1 CHIL @' . $child->xref() . '@', true);

        if ($request->get('goto') === 'new') {
            return new RedirectResponse($child->url());
        }

        return new RedirectResponse($individual->url());
    }

    /**
     * Add a parent to an existing individual (creating a one-parent family).
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function addParent(Request $request, Tree $tree): Response
    {
        $xref   = $request->get('xref', '');
        $gender = $request->get('gender', 'U');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        if ($gender === 'F') {
            $title  = $individual->fullName() . ' - ' . I18N::translate('Add a mother');
            $famtag = 'WIFE';
        } else {
            $title  = $individual->fullName() . ' - ' . I18N::translate('Add a father');
            $famtag = 'HUSB';
        }

        return $this->viewResponse('edit/new-individual', [
            'tree'       => $tree,
            'title'      => $title,
            'nextaction' => 'add_parent_to_individual_action',
            'individual' => $individual,
            'family'     => null,
            'name_fact'  => null,
            'famtag'     => $famtag,
            'gender'     => $gender,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function addParentAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $this->glevels = $request->get('glevels', []);
        $this->tag     = $request->get('tag', []);
        $this->text    = $request->get('text', []);
        $this->islink  = $request->get('islink', []);

        // Create a new family
        $gedcom = "0 @@ FAM\n1 CHIL @" . $individual->xref() . '@';
        $family = $tree->createFamily($gedcom);

        // Link the child to the family
        $individual->createFact('1 FAMC @' . $family->xref() . '@', true);

        // Create a child
        $this->splitSource(); // separate SOUR record from the rest

        $gedcom = '0 @@ INDI';
        $gedcom .= $this->addNewName($request, $tree);
        $gedcom .= $this->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedcom .= $this->addNewFact($request, $tree, $match);
            }
        }
        if ((bool) $request->get('SOUR_INDI')) {
            $gedcom = $this->handleUpdates($gedcom);
        } else {
            $gedcom = $this->updateRest($gedcom);
        }
        $gedcom .= "\n1 FAMS @" . $family->xref() . '@';

        $parent = $tree->createIndividual($gedcom);

        // Link the family to the child
        if ($parent->sex() === 'F') {
            $family->createFact('1 WIFE @' . $parent->xref() . '@', true);
        } else {
            $family->createFact('1 HUSB @' . $parent->xref() . '@', true);
        }

        if ($request->get('goto') === 'new') {
            return new RedirectResponse($parent->url());
        }

        return new RedirectResponse($individual->url());
    }

    /**
     * Add a spouse to an existing individual (creating a new family).
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function addSpouse(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        if ($individual->sex() === 'F') {
            $title  = $individual->fullName() . ' - ' . I18N::translate('Add a husband');
            $famtag = 'HUSB';
            $gender = 'M';
        } else {
            $title  = $individual->fullName() . ' - ' . I18N::translate('Add a wife');
            $famtag = 'WIFE';
            $gender = 'F';
        }

        return $this->viewResponse('edit/new-individual', [
            'tree'       => $tree,
            'title'      => $title,
            'nextaction' => 'add_spouse_to_individual_action',
            'individual' => $individual,
            'family'     => null,
            'name_fact'  => null,
            'famtag'     => $famtag,
            'gender'     => $gender,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function addSpouseAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $sex = $request->get('SEX', 'U');

        $this->glevels = $request->get('glevels', []);
        $this->tag     = $request->get('tag', []);
        $this->text    = $request->get('text', []);
        $this->islink  = $request->get('islink', []);

        $this->splitSource();
        $indi_gedcom = '0 @@ INDI';
        $indi_gedcom .= $this->addNewName($request, $tree);
        $indi_gedcom .= $this->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $indi_gedcom .= $this->addNewFact($request, $tree, $match);
            }
        }
        if ((bool) $request->get('SOUR_INDI')) {
            $indi_gedcom = $this->handleUpdates($indi_gedcom);
        } else {
            $indi_gedcom = $this->updateRest($indi_gedcom);
        }

        $fam_gedcom = '';
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FAMFACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $fam_gedcom .= $this->addNewFact($request, $tree, $match);
            }
        }
        if ((bool) $request->get('SOUR_FAM')) {
            $fam_gedcom = $this->handleUpdates($fam_gedcom);
        } else {
            $fam_gedcom = $this->updateRest($fam_gedcom);
        }

        // Create the new spouse
        $spouse = $tree->createIndividual($indi_gedcom);
        // Create a new family
        if ($sex === 'F') {
            $family = $tree->createFamily("0 @@ FAM\n1 WIFE @" . $spouse->xref() . "@\n1 HUSB @" . $individual->xref() . '@' . $fam_gedcom);
        } else {
            $family = $tree->createFamily("0 @@ FAM\n1 HUSB @" . $spouse->xref() . "@\n1 WIFE @" . $individual->xref() . '@' . $fam_gedcom);
        }
        // Link the spouses to the family
        $spouse->createFact('1 FAMS @' . $family->xref() . '@', true);
        $individual->createFact('1 FAMS @' . $family->xref() . '@', true);

        if ($request->get('goto') === 'new') {
            return new RedirectResponse($spouse->url());
        }

        return new RedirectResponse($individual->url());
    }

    /**
     * Add an unlinked individual
     *
     * @param Tree $tree
     *
     * @return Response
     */
    public function addUnlinked(Tree $tree): Response
    {
        return $this->viewResponse('edit/new-individual', [
            'tree'       => $tree,
            'title'      => I18N::translate('Create an individual'),
            'nextaction' => 'add_unlinked_indi_action',
            'individual' => null,
            'family'     => null,
            'name_fact'  => null,
            'famtag'     => '',
            'gender'     => 'U',
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function addUnlinkedAction(Request $request, Tree $tree): RedirectResponse
    {
        $this->glevels = $request->get('glevels', []);
        $this->tag     = $request->get('tag', []);
        $this->text    = $request->get('text', []);
        $this->islink  = $request->get('islink', []);

        $this->splitSource();
        $gedrec = '0 @@ INDI';
        $gedrec .= $this->addNewName($request, $tree);
        $gedrec .= $this->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedrec .= $this->addNewFact($request, $tree, $match);
            }
        }
        if ((bool) $request->get('SOUR_INDI')) {
            $gedrec = $this->handleUpdates($gedrec);
        } else {
            $gedrec = $this->updateRest($gedrec);
        }

        $new_indi = $tree->createIndividual($gedrec);

        if ($request->get('goto') === 'new') {
            return new RedirectResponse($new_indi->url());
        }

        return new RedirectResponse(route('admin-trees', ['ged' => $tree->name()]));
    }

    /**
     * Edit a name record.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function editName(Request $request, Tree $tree): Response
    {
        $fact_id = $request->get('fact_id', '');
        $xref    = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        // Find the fact to edit
        foreach ($individual->facts() as $fact) {
            if ($fact->id() === $fact_id && $fact->canEdit()) {
                return $this->viewResponse('edit/new-individual', [
                    'tree'       => $tree,
                    'title'      => I18N::translate('Edit the name'),
                    'nextaction' => 'update',
                    'individual' => $individual,
                    'family'     => null,
                    'name_fact'  => $fact,
                    'famtag'     => '',
                    'gender'     => $individual->sex(),
                ]);
            }
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function editNameAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        // @TODO - Move the name-specific code to this function?
        return app(EditGedcomRecordController::class)->updateFact($request, $tree);
    }

    /**
     * Add a new name record.
     *
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function addName(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $title = $individual->fullName() . ' — ' . I18N::translate('Add a name');

        return $this->viewResponse('edit/new-individual', [
            'tree'       => $tree,
            'title'      => $title,
            'nextaction' => 'update',
            'individual' => $individual,
            'family'     => null,
            'name_fact'  => null,
            'famtag'     => '',
            'gender'     => $individual->sex(),
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function addNameAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        // @TODO - Move the name-specific code to this function?

        return app(EditGedcomRecordController::class)->updateFact($request, $tree);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function linkChildToFamily(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        $title = $individual->fullName() . ' - ' . I18N::translate('Link this individual to an existing family as a child');

        return $this->viewResponse('edit/link-child-to-family', [
            'individual' => $individual,
            'title'      => $title,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function linkChildToFamilyAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref  = $request->get('xref', '');
        $famid = $request->get('famid', '');
        $PEDI  = $request->get('PEDI', '');

        $individual = Individual::getInstance($xref, $tree);
        Auth::checkIndividualAccess($individual, true);

        $family = Family::getInstance($famid, $tree);
        Auth::checkFamilyAccess($family, true);

        // Replace any existing child->family link (we may be changing the PEDI);
        $fact_id = '';
        foreach ($individual->facts(['FAMC']) as $fact) {
            if ($family === $fact->target()) {
                $fact_id = $fact->id();
                break;
            }
        }

        $gedcom = GedcomCodePedi::createNewFamcPedi($PEDI, $famid);
        $individual->updateFact($fact_id, $gedcom, true);

        // Only set the family->child link if it does not already exist
        $chil_link_exists = false;
        foreach ($family->facts(['CHIL']) as $fact) {
            if ($individual === $fact->target()) {
                $chil_link_exists = true;
                break;
            }
        }

        if (!$chil_link_exists) {
            $family->createFact('1 CHIL @' . $individual->xref() . '@', true);
        }

        return new RedirectResponse($individual->url());
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function linkSpouseToIndividual(Request $request, Tree $tree): Response
    {
        $xref = $request->get('xref', '');

        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual, true);

        if ($individual->sex() === 'F') {
            $title = $individual->fullName() . ' - ' . I18N::translate('Add a husband using an existing individual');
            $label = I18N::translate('Husband');
        } else {
            $title = $individual->fullName() . ' - ' . I18N::translate('Add a wife using an existing individual');
            $label = I18N::translate('Wife');
        }

        return $this->viewResponse('edit/link-spouse-to-individual', [
            'individual' => $individual,
            'label'      => $label,
            'title'      => $title,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function linkSpouseToIndividualAction(Request $request, Tree $tree): RedirectResponse
    {
        $xref   = $request->get('xref', '');
        $spouse = $request->get('spid', '');

        $individual = Individual::getInstance($xref, $tree);
        Auth::checkIndividualAccess($individual, true);

        $spouse = Individual::getInstance($spouse, $tree);
        Auth::checkIndividualAccess($spouse, true);

        if ($individual->sex() === 'M') {
            $gedcom = "0 @@ FAM\n1 HUSB @" . $individual->xref() . "@\n1 WIFE @" . $spouse->xref() . '@';
        } else {
            $gedcom = "0 @@ FAM\n1 WIFE @" . $individual->xref() . "@\n1 HUSB @" . $spouse->xref() . '@';
        }

        $gedcom .= $this->addNewFact($request, $tree, 'MARR');

        $family = $tree->createFamily($gedcom);

        $individual->createFact('1 FAMS @' . $family->xref() . '@', true);
        $spouse->createFact('1 FAMS @' . $family->xref() . '@', true);

        return new RedirectResponse($family->url());
    }
}
