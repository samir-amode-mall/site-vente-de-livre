DROP FUNCTION IF EXISTS inscription(VARCHAR, VARCHAR, TEXT, VARCHAR, VARCHAR, VARCHAR);

CREATE OR REPLACE FUNCTION inscription(
    p_nom VARCHAR,
    p_prenom VARCHAR,
    p_adresse TEXT,
    p_cp VARCHAR,
    p_ville VARCHAR,
    p_pays VARCHAR
) RETURNS INTEGER AS $$
DECLARE
existing_client INTEGER;
    new_id INTEGER;
BEGIN

SELECT code INTO existing_client FROM clients
WHERE LOWER(nom) = LOWER(p_nom)
  AND LOWER(prenom) = LOWER(p_prenom)
  AND LOWER(adresse) = LOWER(p_adresse);

IF existing_client IS NOT NULL THEN
        RETURN 0;
END IF;

INSERT INTO clients (nom, prenom, adresse, cp, ville, pays)
VALUES (p_nom, p_prenom, p_adresse, p_cp, p_ville, p_pays)
    RETURNING code INTO new_id;

RETURN new_id;
END;
$$ LANGUAGE plpgsql;
