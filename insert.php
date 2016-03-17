<?php

function insertIntoDb(Citation $citation)
{
    $conn = $this->getContainer()->get('doctrine')->getManager()->getConnection();
    $authors = $citation->getAuthors();
    $editors = $citation->getEditors();
    $citationArr = array(
        'citation_type_id' => $citation->getCitationType()->getId(),
        'article_title' => $citation->getArticleTitle(),
        'c_source' => $citation->getCSource(),
        'pub_year' => $citation->getPubYear(),
        'volume' => $citation->getVolume(),
        'issue' => $citation->getIssue(),
        'fpage' => $citation->getFpage(),
        'lpage' => $citation->getLpage(),
        'doi' => $citation->getDoi(),
        'pmid' => $citation->getPmid(),
        'publisher_loc' => $citation->getPublisherLoc(),
        'publisher_name' => $citation->getPublisherName(),
        'conf_date' => $citation->getConfDate(),
        'conf_name' => $citation->getConfName(),
        'conf_loc' => $citation->getConfLoc(),
        'edition' => $citation->getEdition(),
        'etal' => $citation->getEtal(),
    );
    $conn->insert('citations', $citationArr);
    foreach ($authors as $value) {
        $authorArr = array(
            'citation_id' => $citation->getId(),
            'surname' => $value->getSurname(),
            'given_names' => $value->getGivenNames(),
            'collab' => $value->getCollab(),
        );
        $conn->insert('authors', $authorArr);
    }
    foreach ($editors as $value) {
        $editorArr = array(
            'citation_id' => $citation->getId(),
            'surname' => $value->getSurname(),
            'given_names' => $value->getGivenNames(),
        );
        $conn->insert('editors', $editorArr);
    }
}

function toArr($citation)
{
    $citationArr = array(
        $citation->getArticleTitle(),
        $citation->getCSource(),
        $citation->getPubYear(),
        $citation->getVolume(),
        $citation->getIssue(),
        $citation->getFpage(),
        $citation->getLpage(),
        $citation->getDoi(),
        $citation->getPmid(),
        $citation->getPublisherLoc(),
        $citation->getPublisherName(),
        $citation->getConfDate(),
        $citation->getConfName(),
        $citation->getConfLoc(),
        $citation->getEdition(),
        $citation->getEtal(),
        $citation->getCitationType()->getId(),
    );
    return $citationArr;
}

function insertIntoDbEx($citations)
{
    $conn = $this->getContainer()->get('doctrine')->getManager()->getConnection();
    $authors = array();
    $editors = array();

    $citationSql = "INSERT INTO citations (article_title, c_source, pub_year, volume, issue, fpage, lpage, doi, pmid, publisher_loc, publisher_name, conf_date, conf_name, conf_loc, edition, etal, citation_type_id) VALUES ";
    $values = array();

    foreach ($citations as $temCitation) {
        $authors = array_merge($authors, $temCitation->getAuthors()->toArray());
        $editors = array_merge($editors, $temCitation->getEditors()->toArray());
        $temCitation = $this->toArr($temCitation);
        $temCitation = array_map(function($v) use ($conn) {
            return $conn->quote(is_null($v) ? 'null' : $v, \PDO::PARAM_STR);
        }, $temCitation);
        $values[] = "(" . implode(',', $temCitation) .")";
    }
    $values = implode(',', $values);
    $conn->executeUpdate($citationSql.$values);

    $sql2 = "INSERT INTO authors (surname, given_names, collab, citation_id) VALUES ";
    $varues2 = array();
    foreach ($authors as $tmpAuthor) {
        $tmpAuthor = array(
            $tmpAuthor->getSurname(),
            $tmpAuthor->getGivenNames(),
            $tmpAuthor->getCollab(),
            // $tmpAuthor->getCitation()->getId(),
            37882
        );
        $tmpAuthor = array_map(function($v) use ($conn) {
            return $conn->quote(is_null($v) ? 'null' : $v, \PDO::PARAM_STR);
        }, $tmpAuthor);
        $varues2[] = "(" . implode(',', $tmpAuthor) .")";
    }
    $varues2 = implode(',', $varues2);
    $conn->executeUpdate($sql2.$varues2);

    if (count($editors)) {
        $sql3 = "INSERT INTO editors (surname, given_names, citation_id) VALUES ";
        $varues3 = array();
        foreach ($editors as $tmpEditor) {
            $tmpEditor = array(
                $tmpEditor->getSurname(),
                $tmpEditor->getGivenNames(),
                //$tmpEditor->getCitation()->getId(),
                37882
            );
            $tmpEditor = array_map(function($v) use ($conn) {
                return $conn->quote(is_null($v) ? 'null' : $v, \PDO::PARAM_STR);
            }, $tmpEditor);
            $varues3[] = "(" . implode(',', $tmpEditor) .")";
        }
        $varues3 = implode(',', $varues3);
        $conn->executeUpdate($sql3.$varues3);
    }
}
