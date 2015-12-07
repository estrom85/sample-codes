#include "CMaterialLibrary.h"
#include <iostream>

void CMaterialLibrary::load(pugi::xml_node &node)
{
    for(pugi::xml_node material=node.child("material");material;material=material.next_sibling("material"))
    {
        CMaterial mat;
        mat.mID=material.attribute("id").value();
        mat.mEfect.mURL=material.child("instance_effect").attribute("url").value();
        mMaterials.push_back(mat);

    }
}
