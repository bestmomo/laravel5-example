using System.Web.Mvc;

namespace MyProject.Areas.Filemanager
{
    /// <summary>
    /// Register the Filemanager Area
    /// </summary>
    public class DebugAreaRegistration : AreaRegistration
    {
        /// <summary>
        /// Returns the AreaName
        /// </summary>
        public override string AreaName
        {
            get
            {
                return "Filemanager";
            }
        }

        /// <summary>
        /// Registers the area
        /// </summary>
        public override void RegisterArea(AreaRegistrationContext context)
        {
            context.MapRoute(
                "Filemanager_default",
                "Scripts/filemanager/connectors/mvc/filemanager.mvc",
                new { controller = "Filemanager", action = "Index" },
                new string[] { "MyProject.Areas.FilemanagerArea.Controllers" }
            );
        }
    }
}