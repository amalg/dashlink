# DashLink - Administrator Guide

**Version 1.0.0**

A comprehensive guide for administrators on using DashLink to create a centralized launch-pad for external tools and websites in Nextcloud.

---

## Table of Contents

1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [Managing Links](#managing-links)
4. [Group-Based Visibility](#group-based-visibility)
5. [Customizing Hover Effects](#customizing-hover-effects)
6. [Export & Import](#export--import)
7. [Use Cases & Examples](#use-cases--examples)
8. [Best Practices](#best-practices)
9. [Troubleshooting](#troubleshooting)

---

## Introduction

### What is DashLink?

DashLink transforms your Nextcloud dashboard into a powerful launch-pad for external websites and web applications. Instead of searching through bookmarks or typing URLs, your team can access frequently-used tools directly from the dashboard with a single click.

### Key Benefits

- **Centralized Access**: All important links in one place
- **Department-Specific**: Show different links to different teams using group visibility
- **Visual Organization**: Custom icons and descriptions make links easy to identify
- **Time Savings**: Reduce the time spent searching for frequently-used tools
- **Consistent Experience**: Everyone sees the same curated set of tools
- **Easy Management**: Drag & drop reordering, bulk import/export

### Who Should Use This Guide?

This guide is for Nextcloud administrators responsible for:
- Setting up and configuring DashLink
- Managing links for their organization
- Creating department or team-specific link sets
- Training users on available tools

---

## Getting Started

### Accessing the Admin Panel

1. Log in to Nextcloud as an administrator
2. Navigate to **Settings** (click your profile picture in the top-right)
3. In the left sidebar, under **Administration**, click **DashLink**

You'll see the DashLink admin panel with:
- **Link Management Area**: List of all links with edit/delete controls
- **Preview Panel**: Live preview of how links appear to users
- **Add Link Button**: Create new links
- **Export/Import Buttons**: Backup or migrate your configuration

### Understanding the Interface

#### Link List (Left Side)
- Shows all configured links in order
- Each link displays:
  - **Position badge** (drag handle for reordering)
  - **Icon preview**
  - **Title and URL**
  - **Edit/Delete buttons**
  - **Group badges** (if visibility is restricted)

#### Preview Panel (Right Side)
- Shows exactly how links appear on the dashboard
- **Group Filter**: Simulate what different user groups see
- **Effect Preview**: Test hover animations in real-time
- Updates immediately as you make changes

---

## Managing Links

### Creating a New Link

1. Click the **"Add Link"** button
2. Fill in the link details:

#### Required Fields

**Title**
- The display name shown to users
- Keep it short and descriptive (e.g., "Office 365", "Time Tracking")
- Maximum 255 characters

**URL**
- The full website address including `https://`
- Examples:
  - `https://mail.google.com`
  - `https://mycompany.zendesk.com`
  - `https://analytics.example.com/dashboard`

#### Optional Fields

**Description**
- Appears when users hover over the link
- Explain what the tool does or when to use it
- Example: "Submit and track support tickets for IT issues"
- Supports up to 500 characters

**Icon**
- Upload a custom logo (PNG, JPG, GIF, SVG, WebP)
- Maximum file size: 2MB
- Recommended size: 64x64 pixels or larger
- **OR** Download from URL: Paste an icon URL to automatically fetch and save

**Open In**
- **New Tab** (_blank): Opens link in a new browser tab (recommended)
- **Same Tab** (_self): Opens link in the current tab

**Visible to Groups**
- Leave empty to show to **all users**
- Select specific groups to restrict visibility
- See [Group-Based Visibility](#group-based-visibility) for detailed strategies

3. Click **"Save"** to create the link

### Editing a Link

1. Click the **edit icon** (pencil) next to any link
2. Modify any field
3. Click **"Save"** to apply changes

**Note**: Changes are immediately visible to all users who refresh their dashboard.

### Deleting a Link

1. Click the **delete icon** (trash can) next to the link
2. Confirm the deletion in the popup dialog

**Warning**: Deletion is permanent and cannot be undone. The icon file is also permanently deleted.

### Reordering Links

Links appear in the order you specify. To reorder:

1. **Drag & Drop**: Click and hold the position badge, then drag to the desired position
2. Release to drop the link in its new position
3. Changes are saved automatically

**Tip**: Put the most frequently-used links at the top for easy access.

---

## Group-Based Visibility

### Overview

Group-based visibility allows you to show different links to different teams, departments, or roles. This is one of DashLink's most powerful features for organizations.

### How It Works

When you assign groups to a link:
- **Only members of those groups** can see the link on their dashboard
- Users in multiple groups see all links from their groups
- Links with **no groups assigned** are visible to **everyone**

### Common Visibility Strategies

#### Strategy 1: Department-Specific Tools

**Scenario**: Different departments use different tools

**Implementation**:
```
HR Links (Visible to: "HR" group only)
- ADP Payroll
- BambooHR
- Employee Portal

IT Links (Visible to: "IT" group only)
- Jira Service Desk
- Datadog Monitoring
- AWS Console

Finance Links (Visible to: "Finance" group only)
- QuickBooks
- Expensify
- Financial Dashboard
```

#### Strategy 2: Role-Based Access

**Scenario**: Tools are restricted by job role

**Implementation**:
```
Management Links (Visible to: "Managers" group)
- Analytics Dashboard
- Team Performance Reports
- Budget Planning Tool

All Staff Links (Visible to: everyone - no groups)
- Company Directory
- Time Off Request
- Internal Wiki
```

#### Strategy 3: Project Teams

**Scenario**: Project-specific collaboration tools

**Implementation**:
```
Project Alpha (Visible to: "ProjectAlpha" group)
- Alpha Jira Board
- Alpha Slack Channel
- Alpha Drive Folder

Project Beta (Visible to: "ProjectBeta" group)
- Beta Trello Board
- Beta Teams Channel
- Beta SharePoint
```

#### Strategy 4: Hybrid Approach

**Scenario**: Mix of universal and targeted links

**Implementation**:
```
Everyone sees:
- Company Intranet
- Help Desk
- Email

Sales team also sees:
- Salesforce CRM
- Lead Pipeline
- Sales Reports

Support team also sees:
- Zendesk
- Customer Database
- Support Metrics
```

### Testing Group Visibility

Use the **"Simulate Group Filter"** dropdown in the preview panel:

1. Select a group from the dropdown
2. The preview shows only links visible to that group
3. Select "All Groups" to see all links
4. This helps verify your visibility configuration before users see it

---

## Customizing Hover Effects

### Available Effects

DashLink includes three built-in hover animations:

#### 1. Blur Overlay
- **Appearance**: Description appears over a blurred version of the icon
- **Best For**: Links with distinctive logos/icons
- **User Experience**: Clean and modern, focuses on the description text

#### 2. 3D Card Flip
- **Appearance**: Card flips 180° to reveal description on the back
- **Best For**: Creating an interactive, playful experience
- **User Experience**: Engaging animation, clear separation of icon and description

#### 3. Slide Panel
- **Appearance**: Panel slides up from bottom with description
- **Best For**: Subtle animations, maintaining icon visibility
- **User Experience**: Smooth and professional, icon remains partially visible

### Changing the Global Effect

1. In the admin panel, locate the **"Hover Effect"** dropdown
2. Select an effect from the list
3. Watch the preview panel update in real-time
4. Click **"Save"** to apply to all users

**Note**: The effect applies to all links globally. Individual links cannot have different effects.

### Choosing the Right Effect

**Blur Overlay** - Recommended for:
- Professional corporate environments
- Links with strong, recognizable brand logos
- When description text is the priority

**3D Card Flip** - Recommended for:
- Creative teams or modern workplaces
- When you want an engaging, interactive feel
- Educational or training environments

**Slide Panel** - Recommended for:
- Conservative or traditional organizations
- When you want subtle, non-distracting animations
- Environments where users prefer minimal motion

---

## Export & Import

### Why Export/Import?

- **Backup**: Save your link configuration as a file
- **Migration**: Move links between Nextcloud instances
- **Sharing**: Share link sets with other organizations
- **Version Control**: Keep historical snapshots of your configuration

### Exporting Links

1. Click the **"Export"** button in the admin panel
2. A JSON file downloads automatically: `dashlink-export-YYYY-MM-DD.json`
3. Store this file safely

**What's Included in Export**:
- All link titles, URLs, and descriptions
- Group visibility settings
- Open-in settings (new tab vs same tab)
- **Icon URLs**: Absolute URLs pointing to your Nextcloud instance

**What's NOT Included**:
- Icon image files themselves (only URLs to them)
- Global hover effect setting
- User-specific data

### Importing Links

1. Click the **"Import"** button
2. Select a JSON file to import
3. Review the import summary:
   - **Imported**: Number of new links added
   - **Skipped**: Number of duplicates detected
   - **Errors**: Any issues encountered (e.g., failed icon downloads)

#### Duplicate Detection

DashLink automatically detects duplicates by:
- **Title**: Links with the same title
- **URL**: Links pointing to the same address

Duplicates are **skipped** to prevent redundant entries.

#### Icon Handling During Import

If the JSON includes `iconUrl` fields:
- DashLink attempts to download icons from those URLs
- Successfully downloaded icons are saved locally
- Failed downloads are logged in the import summary
- Links are created even if icon download fails

**Tip**: When migrating between instances, import immediately after export while icon URLs are still valid.

### Import Use Cases

#### Use Case 1: Migrating to a New Instance

```
Old Instance (old.company.com):
1. Export links

New Instance (nextcloud.company.com):
2. Install DashLink
3. Import the JSON file
4. Icons may fail to download (old URLs)
5. Re-upload icons manually or use the "Download from URL" feature
```

#### Use Case 2: Sharing with Other Organizations

```
Organization A:
1. Export links
2. Share JSON with Organization B

Organization B:
3. Import JSON
4. Duplicates are skipped automatically
5. Customize groups to match their structure
```

#### Use Case 3: Template Library

Create standard link sets for common scenarios:

**Template: Remote Team Starter Pack**
```json
- Zoom Video Conferencing
- Slack Team Chat
- Asana Project Management
- Google Drive
```

Export and reuse for new teams or clients.

---

## Use Cases & Examples

### Use Case 1: IT Department Launch-Pad

**Scenario**: IT team needs quick access to multiple monitoring and management tools throughout the day.

**Implementation**:
```
Visible to: IT group only

Infrastructure Tools:
🖥️ AWS Console - "Manage cloud infrastructure and EC2 instances"
📊 Datadog - "Real-time monitoring and performance metrics"
🔧 Jira Service Desk - "Track and resolve internal IT tickets"
🔐 Vault - "Secure secrets and credential management"
📦 GitHub - "Code repositories and CI/CD pipelines"

Support Tools:
🎫 Zendesk - "Customer support ticket management"
📚 Confluence - "IT documentation and runbooks"
🔍 Splunk - "Log analysis and security monitoring"
```

**Benefits**:
- IT staff save 10-15 minutes daily not searching for tool URLs
- Onboarding new IT staff is faster with centralized tool directory
- Reduced mistakes from typing wrong URLs
- Encourages use of proper tools instead of workarounds

---

### Use Case 2: Multi-Department Organization

**Scenario**: Company with HR, Sales, Finance, and Operations departments, each needing different tools.

**Implementation**:

**HR Department** (Visible to: HR group)
```
👥 BambooHR - "Employee records and performance reviews"
💰 ADP Payroll - "Process payroll and manage benefits"
📋 Workday - "Recruiting and applicant tracking"
📊 Culture Amp - "Employee engagement surveys"
```

**Sales Department** (Visible to: Sales group)
```
💼 Salesforce - "CRM and opportunity pipeline"
📞 HubSpot - "Marketing automation and lead nurturing"
📈 Tableau Sales Dashboard - "Real-time sales metrics"
📧 LinkedIn Sales Navigator - "Prospect research"
```

**Finance Department** (Visible to: Finance group)
```
💵 QuickBooks - "Accounting and financial management"
🧾 Expensify - "Expense reporting and reimbursements"
📊 NetSuite - "ERP and financial planning"
🏦 Bill.com - "Accounts payable automation"
```

**All Staff** (Visible to: everyone)
```
📧 Company Email - "Microsoft 365 Webmail"
📚 Company Wiki - "Internal documentation"
🎫 IT Help Desk - "Submit IT support requests"
📅 Resource Booking - "Conference rooms and equipment"
```

**Benefits**:
- Each department sees only relevant tools
- Eliminates clutter from irrelevant links
- Reinforces departmental workflows
- Easy to add tools as company grows

---

### Use Case 3: Client Portal for Service Providers

**Scenario**: Web agency serving multiple clients, each with different project tools.

**Implementation**:

**Client A Team** (Visible to: ClientA group)
```
📋 Client A Jira - "Project tasks and sprints"
💬 Client A Slack - "Team communication channel"
📁 Client A Drive - "Shared files and deliverables"
🎨 Client A Figma - "Design collaboration"
📊 Client A Analytics - "Website performance dashboard"
```

**Client B Team** (Visible to: ClientB group)
```
📋 Client B Trello - "Project management board"
💬 Client B Teams - "Video calls and chat"
📁 Client B SharePoint - "Document library"
🎨 Client B Adobe XD - "Design prototypes"
📊 Client B Reports - "Monthly performance reports"
```

**Internal Team** (Visible to: Staff group)
```
💼 Time Tracking - "Log billable hours"
📊 Agency Dashboard - "All project overviews"
🎫 Internal Jira - "Internal tasks and bugs"
```

**Benefits**:
- Team members see only their assigned client tools
- Prevents confusion between similar client portals
- Simplifies context-switching between clients
- Easy to add/remove team members from projects

---

### Use Case 4: Educational Institution

**Scenario**: University with students, faculty, and administrative staff.

**Implementation**:

**Students** (Visible to: Students group)
```
📚 Canvas LMS - "Access courses and assignments"
📖 Library Portal - "Search catalogs and reserve books"
💳 Student Account - "View grades and manage registration"
🎓 Career Services - "Job board and resume help"
🏃 Recreation Center - "Book gym facilities"
```

**Faculty** (Visible to: Faculty group)
```
📝 Canvas Instructor - "Manage courses and grade assignments"
📊 Banner Admin - "Student records and enrollment"
📧 Faculty Email - "Institutional email"
📚 Research Portal - "Grant applications and IRB"
🗓️ Faculty Calendar - "Office hours and meetings"
```

**Administrative Staff** (Visible to: Admin group)
```
💼 Workday HR - "Payroll and benefits"
📊 Banner System - "Student information system"
🎫 Help Desk - "IT support tickets"
📧 Exchange Admin - "Email administration"
📈 Analytics Dashboard - "Enrollment and retention metrics"
```

**Everyone**
```
🏫 University Homepage - "Official website"
📰 Campus News - "Announcements and events"
🗺️ Campus Map - "Building locations and directions"
📞 Directory - "Faculty and staff contact information"
```

**Benefits**:
- Each user role sees appropriate tools
- Reduces support burden from users accessing wrong systems
- Streamlines daily workflows for all user types
- Single location for all university web tools

---

### Use Case 5: Healthcare Organization

**Scenario**: Hospital with doctors, nurses, administrative staff, and billing department.

**Implementation**:

**Clinical Staff** (Doctors, Nurses groups)
```
🏥 EMR System - "Electronic medical records"
💊 Pharmacy Portal - "Prescription management"
📋 Lab Results - "Patient test results"
🗓️ Scheduling - "Patient appointments"
📚 Clinical Guidelines - "Treatment protocols"
```

**Billing Department** (Visible to: Billing group)
```
💰 Claims Portal - "Insurance claim submission"
📊 Revenue Cycle - "Billing analytics"
🏦 Payment Gateway - "Patient payment processing"
📋 Coding Reference - "ICD-10 and CPT codes"
```

**Administrative** (Visible to: Admin group)
```
👥 HR Portal - "Staff management"
📊 Dashboard - "Hospital operations metrics"
🎫 IT Support - "Technical assistance"
📧 Email - "Staff communication"
```

**Benefits**:
- HIPAA compliance through role-based access
- Clinical staff focused on patient care tools
- Billing isolated from clinical workflows
- Reduces training complexity for new staff

---

### Use Case 6: Remote Team Collaboration Hub

**Scenario**: Fully remote company needs centralized access to collaboration tools.

**Implementation**:

**All Remote Staff**
```
💬 Slack - "Team chat and channels"
📹 Zoom - "Video conferences and webinars"
📁 Google Drive - "Shared documents and files"
📋 Asana - "Project and task management"
🕐 Toggl - "Time tracking for projects"
📊 Miro - "Virtual whiteboard collaboration"
📧 Gmail - "Company email"
🗓️ Google Calendar - "Meetings and events"
```

**Benefits**:
- Single location for all collaboration tools
- Especially valuable for remote workers who switch tools frequently
- Reduces time lost searching for meeting links
- Helps new hires discover all available tools
- Creates consistency across the organization

---

## Best Practices

### Icon Selection

✅ **Do**:
- Use high-quality, recognizable logos
- Maintain consistent icon sizes (64x64 or larger)
- Use official brand logos when available
- Ensure icons work well in both light and dark mode

❌ **Don't**:
- Use low-resolution or pixelated images
- Mix icon styles (some with borders, some without)
- Use generic icons for well-known brands
- Forget to test in dark mode

### Link Organization

✅ **Do**:
- Order links by frequency of use (most used first)
- Group related tools together
- Use clear, concise titles
- Write helpful descriptions that explain **when** to use the tool

❌ **Don't**:
- Create more than 10-15 links per group (too overwhelming)
- Use technical jargon in titles
- Leave descriptions empty
- Create duplicate links with slightly different names

### Group Management

✅ **Do**:
- Keep group names aligned with your organization structure
- Document which groups see which links
- Review group assignments quarterly
- Test visibility with the preview panel before deploying

❌ **Don't**:
- Create too many granular groups (manage at department level)
- Forget to update when employees change roles
- Show the same link to multiple overlapping groups (creates duplicates)
- Leave groups empty with no links

### Description Writing

✅ **Good Examples**:
- "Submit IT support tickets for hardware, software, or access issues"
- "Track project tasks, assign team members, and monitor sprint progress"
- "View real-time sales pipeline and quarterly revenue forecasts"

❌ **Bad Examples**:
- "IT stuff" (too vague)
- "Click here to go to Jira" (obvious, unhelpful)
- "The main CRM system used by the sales department for managing customer relationships and tracking sales opportunities across all regions" (too long)

### Maintenance Schedule

**Weekly**:
- Review new link requests from staff
- Check for broken links or services that have moved

**Monthly**:
- Audit group memberships as staff change roles
- Review link usage and remove rarely-used tools
- Update descriptions based on user feedback

**Quarterly**:
- Export a backup of your configuration
- Review and consolidate groups if needed
- Survey users on which links are most valuable

**Annually**:
- Complete audit of all links, icons, and groups
- Update for organizational changes (mergers, restructuring)
- Review and revise your overall linking strategy

---

## Troubleshooting

### Users Can't See Links They Should See

**Possible Causes**:
1. User is not in the assigned group
2. Link is disabled
3. User hasn't refreshed their dashboard

**Solutions**:
1. Verify group membership in Nextcloud user management
2. Check the link is enabled in the admin panel
3. Ask user to refresh their browser (F5 or Ctrl+R)
4. Use the preview panel to simulate what that group sees

---

### Icons Not Displaying

**Possible Causes**:
1. Icon file was corrupted during upload
2. Icon exceeds 2MB file size limit
3. Unsupported file format
4. Browser cache showing old icon

**Solutions**:
1. Delete and re-upload the icon
2. Compress or resize the icon file
3. Use PNG, JPG, GIF, SVG, or WebP format only
4. Clear browser cache or hard refresh (Ctrl+Shift+R)

---

### Import Fails or Shows Errors

**Possible Causes**:
1. JSON file is corrupted or invalid
2. Icon URLs in JSON are unreachable
3. Permissions issue writing to app data directory

**Solutions**:
1. Verify the JSON file is valid (check with a JSON validator)
2. Import will still work, icons just won't download automatically
3. Check Nextcloud logs for permission errors
4. Re-upload icons manually after import

---

### Links Open in Wrong Tab

**Possible Causes**:
1. "Open In" setting is incorrect
2. Browser is blocking new tabs

**Solutions**:
1. Edit the link and change "Open In" to "New Tab"
2. Check browser popup blocker settings
3. Some browsers may override this setting based on user preferences

---

### Hover Effect Not Working

**Possible Causes**:
1. JavaScript is disabled
2. Browser compatibility issue
3. Nextcloud theme conflict

**Solutions**:
1. Enable JavaScript in browser settings
2. Try a different browser (Chrome, Firefox, Edge, Safari all supported)
3. Disable browser extensions temporarily to test
4. Report issue on GitHub with browser version details

---

### Too Many Links, Dashboard Feels Cluttered

**Solutions**:
1. **Use Groups More Aggressively**: Split links by department/role
2. **Review Necessity**: Remove rarely-used links
3. **Create Priority Tiers**:
   - Top 5 links: Daily-use tools
   - Next 5 links: Weekly-use tools
   - Remove: Monthly or less frequent
4. **External Link Pages**: For rarely-used tools, create a separate wiki page instead

---

### Getting Started Recommendations

If you're setting up DashLink for the first time:

**Week 1**: Start Small
- Add 3-5 universal links everyone needs
- Test with a small group
- Gather feedback

**Week 2**: Expand
- Add department-specific links
- Set up group visibility
- Train administrators

**Week 3**: Refine
- Adjust based on user feedback
- Add descriptions and better icons
- Optimize link order

**Week 4**: Full Deployment
- Roll out to entire organization
- Create documentation for users
- Establish maintenance schedule

---

## Additional Resources

- **Technical Documentation**: See `/docs` folder for implementation details
- **GitHub Issues**: Report bugs or request features
- **Nextcloud Community**: General Nextcloud support
- **Effect System Guide**: Learn how to add custom hover effects

---

## Feedback and Support

We'd love to hear how you're using DashLink!

- **Feature Requests**: [GitHub Issues](https://github.com/lexioj/dashlink/issues)
- **Bug Reports**: [GitHub Issues](https://github.com/lexioj/dashlink/issues)
- **Discussions**: [GitHub Discussions](https://github.com/lexioj/dashlink/discussions)

---

**DashLink v1.0.0** | Licensed under AGPL-3.0-or-later | [View on GitHub](https://github.com/lexioj/dashlink)
