<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "JobPosting",
    "title": "<?php echo $job_title; ?>",
    "description": <?php echo json_encode($job_description); ?>,
    "datePosted": "<?php echo $job_date_published_iso8601; ?>",
    "directApply": true,
    "employmentType": "<?php echo $job_employment_type; ?>",
    "hiringOrganization": {
        "@type": "Organization",
        "name": "NSC Staffing"
    },
    "jobLocation": {
        "@type": "Place",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "<?php echo $job_location_city; ?>",
            "addressRegion": "<?php echo $job_location_state; ?>",
            "postalCode": "<?php echo $job_location_postal_code; ?>",
            "addressCountry": "<?php echo $job_location_country_code; ?>"
        }
    }
}
</script>
