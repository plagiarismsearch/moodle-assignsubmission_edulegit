# moodle-assignsubmission_edulegit

EduLegit workspace is a Moodle assignment submission plugin

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/plagiarismsearch/assignsubmission_edulegit/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/plagiarismsearch/assignsubmission_edulegit/?branch=master)

**Supported Moodle versions:** 4.2 - 4.5  
**Supported PHP versions:** 8.1 - 8.3  
**Moodle plugins directory:** https://moodle.org/plugins/assignsubmission_edulegit

Author: Alex Crosby <developer@plagiarismsearch.com>  
Copyright: Edulegit.com, https://edulegit.com

EduLegit is an innovative AI management control system that aims to assist teachers in supervising students and ensuring seamless workflow and increased student involvement in the
learning process. The supervision is handled with the most advanced tools, including but not limited to:

* AI motion monitoring
* Al control alerts
* AI text check
* Screen monitoring
* Plagiarism check
* Work location control
* Session and time control

### Quick install

1. Get the latest release (zip file) on [GitHub](https://github.com/plagiarismsearch/moodle-assignsubmission_edulegit/releases) or
   the [Moodle plugins directory](https://moodle.org/plugins/assignsubmission_edulegit).
2. Follow the instructions described [here](https://docs.moodle.org/37/en/Installing_plugins) to install the plugin.
3. Unpack the files into the `/mod/assign/submission/edulegit` Moodle directory.
4. Configure the EduLegit workspace plugin under Admin **Site administration > Plugins > Submission plugins > EduLegit workspace**.
5. Login as Organization administrator and navigate to [EduLegit API Token page](https://app.edulegit.com/account/api) to get your API token.

### Enabling Moodle External Services for Webhooks (Additional Steps)

1. Navigate to **Site administration > Advanced features** and enable the **Web services** feature.
2. Go to **Site administration > Server > Web services > Manage tokens** and click the **Add** button to generate a new token.
3. Copy the generated token and save it into the `Web service token` field in the plugin settings on the page:  
   **Site administration > Plugins > Submission plugins > EduLegit workspace**.


### Data Deletion Request Process for EduLegit

If users need to request the deletion of their data from EduLegit, they can follow these steps:

1. **Visit the EduLegit Website** [https://edulegit.com/](https://edulegit.com/).
2. **Log In**: Use your credentials to log in to your EduLegit account.
3. Navigate to the **Account** page in your profile.
4. Click the **Delete** button.
5. Read the instructions and confirm deletion.

Additionally, **Administrators** can delete user information manually via the **EduLegit Administration Page**.
