use kenyaemr_datatools;
SELECT *,
CASE
	WHEN DWHValidation.HIV_DiagnosisDate_Validation = 'Invalid' THEN 'Invalid'
    WHEN DWHValidation.HIV_EnrollmentDate_Validation = 'Invalid' THEN 'Invalid'
    WHEN DWHValidation.HIV_ARTStartDate_Validation = 'Invalid' THEN 'Invalid'
    WHEN DWHValidation.HIV_FirstVLDate_Validation = 'Invalid' THEN 'Invalid'
    WHEN DWHValidation.HIV_LastVLDate_Validation = 'Invalid' THEN 'Invalid'
    WHEN DWHValidation.date_confirmed_hiv_positive = 'Missing' THEN 'Invalid'
    WHEN DWHValidation.enroll_date = 'Missing' THEN 'Invalid'
    #WHEN DWHValidation.ARTStartDate = 'Missing' THEN 'Invalid'
	WHEN datediff(CURDATE(), STR_TO_DATE(DWHValidation.ARTStartDate,'%d-%b-%Y')) > 200 and  DWHValidation.First_VL_Date = 'Missing' THEN 'Invalid'
    ELSE 'Valid'
END as RecordValid
FROM (
SELECT *, 
round(DATEDIFF(CURRENT_DATE, STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y'))/365.25, 1) as Years_on_ART, 
ifnull(DATEDIFF(CURRENT_DATE, STR_TO_DATE(TX_CURR.Last_VL_Date,'%d-%b-%Y')), 'Missing') as days_since_lastVL,
CASE 
	WHEN STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') <= STR_TO_DATE(TX_CURR.DOB,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') < '01/01/1984' THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') > STR_TO_DATE(TX_CURR.enroll_date,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') > now() THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') = "Missing" THEN 'Invalid'
    ELSE 'Valid'
END AS HIV_DiagnosisDate_Validation,
CASE 
	WHEN STR_TO_DATE(TX_CURR.enroll_date,'%d-%b-%Y') <= STR_TO_DATE(TX_CURR.DOB,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.enroll_date,'%d-%b-%Y') < '01/01/2000' THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.enroll_date,'%d-%b-%Y') > now() THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.enroll_date,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') THEN 'Invalid'
    ELSE 'Valid'
END AS HIV_EnrollmentDate_Validation,
CASE 
	WHEN STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y') <= STR_TO_DATE(TX_CURR.DOB,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.enroll_date,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y') < '01/01/2004' THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y') > now() THEN 'Invalid'
    ELSE 'Valid'
END AS HIV_ARTStartDate_Validation,
CASE 
	WHEN STR_TO_DATE(TX_CURR.First_VL_date,'%d-%b-%Y') <= STR_TO_DATE(TX_CURR.DOB,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.First_VL_date,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.First_VL_date,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.enroll_date,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.First_VL_date,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y') THEN 'Invalid'
    WHEN datediff(CURDATE(), STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y')) > 200 and  TX_CURR.First_VL_Date = 'Missing' THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.First_VL_date,'%d-%b-%Y') > now() THEN 'Invalid'
    ELSE 'Valid'
END AS HIV_FirstVLDate_Validation,
CASE 
	WHEN STR_TO_DATE(TX_CURR.Last_VL_date,'%d-%b-%Y') <= STR_TO_DATE(TX_CURR.DOB,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.Last_VL_date,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.date_confirmed_hiv_positive,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.Last_VL_date,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.enroll_date,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.Last_VL_date,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.ARTStartDate,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.Last_VL_date,'%d-%b-%Y') < STR_TO_DATE(TX_CURR.First_VL_date,'%d-%b-%Y') THEN 'Invalid'
    WHEN STR_TO_DATE(TX_CURR.Last_VL_date,'%d-%b-%Y') > now() THEN 'Invalid'
    ELSE 'Valid'
END AS HIV_LastVLDate_Validation
FROM (
	SELECT (select siteCode from default_facility_info) MFL, (select Facilityname from default_facility_info) Facility,
	 I.unique_patient_no CCC_Number, round(DATEDIFF(LAST_DAY(now() - INTERVAL 1 MONTH), A.dob)/365.25, 2) AS ageInYears, A.Gender, ifnull(date_format(A.dob,'%d-%b-%Y'), 'Missing') DOB, 
    ifnull(date_format(E.date_confirmed_hiv_positive,'%d-%b-%Y'), 'Missing') date_confirmed_hiv_positive,
    ifnull(date_format(A.enroll_date,'%d-%b-%Y'), 'Missing') enroll_date,
	CASE 
		WHEN (select min(date_started_art_at_transferring_facility) from hiv_enrollment where patient_id = A.patient_id) IS NOT NULL
				AND (select min(date_started_art_at_transferring_facility) from hiv_enrollment where patient_id = A.patient_id) < 
				(select min(date_started) from drug_event where patient_id = A.patient_id)
		THEN ifnull(date_format((select min(date_started_art_at_transferring_facility) from hiv_enrollment where patient_id = A.patient_id),'%d-%b-%Y'),'Missing')
		ELSE ifnull(date_format((select min(date_started) from drug_event where patient_id = A.patient_id),'%d-%b-%Y'),'Missing') 
	END AS ARTStartDate,
	(select who_stage from kenyaemr_datatools.hiv_followup where patient_id = A.patient_id order by visit_date asc LIMIT 1) AS baselineWHO,
    (select phone_number from kenyaemr_datatools.patient_demographics where patient_id = A.patient_id order by I.patient_id asc LIMIT 1) AS Phone_Number,
    (select marital_status from kenyaemr_datatools.patient_demographics where patient_id = A.patient_id order by I.patient_id asc LIMIT 1) AS Marital_Status,
	(select given_name from kenyaemr_datatools.patient_demographics where patient_id = A.patient_id order by I.patient_id asc LIMIT 1) AS FirstName,
   (select middle_name from kenyaemr_datatools.patient_demographics where patient_id = A.patient_id order by I.patient_id asc LIMIT 1) AS MiddleName,
   (select family_name from kenyaemr_datatools.patient_demographics where patient_id = A.patient_id order by I.patient_id asc LIMIT 1) AS LastName,
    (select test_result from laboratory_extract where patient_id = A.patient_id and lab_test = 'CD4 Count' order by visit_date asc LIMIT 1) baselineCD4,
    CASE
		WHEN H.test_result IS NOT NULL THEN H.test_result
		WHEN DATEDIFF(LAST_DAY(now() - INTERVAL 1 MONTH), 
			(
				CASE 
					WHEN (select min(date_started_art_at_transferring_facility) from hiv_enrollment where patient_id = A.patient_id) IS NOT NULL
							AND (select min(date_started_art_at_transferring_facility) from hiv_enrollment where patient_id = A.patient_id) < 
							(select min(date_started) from drug_event where patient_id = A.patient_id)
					THEN ifnull((select min(date_started_art_at_transferring_facility) from hiv_enrollment where patient_id = A.patient_id),'Missing')
					ELSE ifnull((select min(date_started) from drug_event where patient_id = A.patient_id),'Missing') 
				END #ARTSTARTDATE
			)
		) < 183 THEN 'Not Eligible'
        ELSE ifnull(H.test_result,'Missing')
	END First_VL_Result,
    ifnull(date_format(H.visit_date,'%d-%b-%Y'),'Missing') First_VL_Date,
    ifnull(B.test_result,'Missing') Last_VL_Result, ifnull(date_format(B.visit_date,'%d-%b-%Y'),'Missing') Last_VL_Date,
    if(D.person_present is null or D.person_present='', 'Missing', D.person_present) person_present, D.weight, D.height, 
	CASE 
		WHEN D.weight/ ((D.height/100) * (D.height/100)) < 18.5 THEN 'Underweight'
		WHEN D.weight/ ((D.height/100) * (D.height/100)) between 18.5 and 25 THEN 'Normal'
		WHEN D.weight/ ((D.height/100) * (D.height/100)) > 25 THEN 'Overweight'
	END as nutritional_status, #D.nutritional_status,
     D.temperature, D.muac,
    D.population_type, D.key_population_type, D.who_stage AS currentWHO, D.presenting_complaints,
    D.on_anti_tb_drugs, D.on_ipt, D.ever_on_ipt, ifnull(date_format(G.visit_date,'%d-%b-%Y'),'Missing') IPTStartDate,
    CASE 
		WHEN F.outcome = 1267 THEN 'Completed'
        WHEN F.outcome = 5240 THEN 'Lost to followup'
        WHEN F.outcome = 159836 THEN 'Discontinue'
        WHEN F.outcome = 160034 THEN 'Died'
        WHEN F.outcome = 159492 THEN 'Transferred Out'
        WHEN F.outcome = 112141 THEN 'Tuberculosis'
        WHEN F.outcome = 102 THEN 'Drug Toxicity'
        ELSE 'Missing'
	END AS IPTOutcome, 
    ifnull(date_format(F.visit_date,'%d-%b-%Y'),'Missing') IPTOutcomeDate,
    IF(D.tb_status IS NULL or D.tb_status = '', 'Not done', D.tb_status) TB_Status, D.has_known_allergies, D.has_chronic_illnesses_cormobidities, D.has_adverse_drug_reaction,
    D.pregnancy_status, D.wants_pregnancy, D.family_planning_status, D.family_planning_method, D.ctx_dispensed, ifnull(C.regimen, 'Missing') current_regimen, C.regimen_line, D.arv_adherence, D.cacx_screening,
	CASE 
		WHEN C.regimen IS NULL THEN 'Missing'
        WHEN I.value_datetime > A.latest_vis_date and I.value_datetime < latest_tca THEN 
			CASE WHEN round(datediff(I.value_datetime, A.latest_vis_date)/30) = 0 THEN 1 ELSE round(datediff(I.value_datetime, A.latest_vis_date)/30) END
        ELSE 
			CASE WHEN round(datediff(latest_tca, A.latest_vis_date)/30) = 0 THEN 1 ELSE round(datediff(latest_tca, A.latest_vis_date)/30) END
		#WHEN round(datediff(latest_tca, A.latest_vis_date)/30) = 0 THEN 1 
        #ELSE round(datediff(latest_tca, A.latest_vis_date)/30) 
	END AS MMD,
	D.stability, 
    date_format(D.visit_date,'%d-%b-%Y') last_visit_date, date_format(D.next_appointment_date,'%d-%b-%Y') next_appointment_date, 
    round(datediff(D.next_appointment_date, D.visit_date)/30) App_Months, D.next_appointment_reason,
    CASE WHEN I.value_datetime > D.visit_date THEN date_format(I.value_datetime,'%d-%b-%Y') ELSE '' END AS refill_date, D.differentiated_care,
    CASE WHEN MCH.program IS NOT NULL THEN MCH.program ELSE 'CCC' END Program, MCH.MCH_Date_enrolled, MCH.LMP, MCH.EDD
	FROM current_in_care A 
	inner join kenyaemr_datatools.patient_demographics I on A.patient_id = I.patient_id 
    LEFT JOIN 
	(select * from (select * from laboratory_extract where lab_test = 'HIV VIRAL LOAD' order by visit_date desc) X group by X.patient_id) B on A.patient_id = B.patient_id
	LEFT JOIN
	(select * from (select * from drug_event where program = 'HIV' order by visit_date desc) Y group by Y.patient_id) C on A.patient_id = C.patient_id
	LEFT JOIN
	(select * from (select * from hiv_followup where person_present <> '' order by visit_date desc) Z group by Z.patient_id) D ON A.patient_id = D.patient_id
	LEFT JOIN
	(select * from (select * from hiv_enrollment order by visit_date asc) W group by W.patient_id) E on A.patient_id = E.patient_id
    LEFT JOIN
    (select * from (select * from kenyaemr_etl.etl_ipt_outcome order by visit_date desc) V group by V.patient_id) F on A.patient_id = F.patient_id
    LEFT JOIN
    (select * from (select * from kenyaemr_etl.etl_ipt_initiation order by visit_date desc) U group by U.patient_id) G on A.patient_id = G.patient_id
    LEFT JOIN 
	(select * from (select * from laboratory_extract where lab_test = 'HIV VIRAL LOAD' order by visit_date asc) T group by T.patient_id) H on A.patient_id = H.patient_id
    LEFT JOIN
    (select * from (select person_id, value_datetime from openmrs.obs where concept_id=162549 order by value_datetime desc) S group by S.person_id) I on A.patient_id = I.person_id
    LEFT JOIN
    (
		SELECT pp.patient_id, date_format(pp.date_enrolled,'%d-%b-%Y') MCH_Date_enrolled, date_format(mchEnrol.lmp,'%d-%b-%Y') LMP, date_format(date_add(mchEnrol.lmp, INTERVAL 280 DAY),'%d-%b-%Y') EDD, 'MCH' program 
		FROM openmrs.patient_program pp inner join kenyaemr_datatools.mch_enrollment mchEnrol on pp.patient_id = mchEnrol.patient_id
		where program_id=4 #MCH Mother Services
		and (date_completed >= LAST_DAY(now() - INTERVAL 1 MONTH) OR date_completed is null) 
		and voided=0
    ) MCH ON A.patient_id = MCH.patient_id
	) TX_CURR) DWHValidation ;
    
