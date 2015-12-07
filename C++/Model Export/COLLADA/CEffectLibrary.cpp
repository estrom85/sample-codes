#include "CEffectLibrary.h"
#include <sstream>

void CEffectLibrary::load(pugi::xml_node &node)
{
    std::stringstream ss;

    for(pugi::xml_node effect_node=node.child("effect");
        effect_node; effect_node=effect_node.next_sibling("effect"))
    {
        CEffect effect;
        effect.mID=effect_node.attribute("id").value();

        pugi::xml_node technique=effect_node.child("profile_COMMON").child("technique");

        if(technique.child("phong")) technique=technique.child("phong");
        else if(technique.child("blin")) technique=technique.child("blin");
        else if(technique.child("lambert")) technique=technique.child("lambert");
        else return;
        float temp=1.0f;

        //emmision
        if(technique.child("emission"))
        {
            ss.clear();
            ss<<technique.child("emission").child("color").child_value();

            for(int i=0;ss>>temp&&i<4;i++)
            {
                effect.mEmmision.mColor[i]=temp;
            }
        }


        //ambient
        if(technique.child("ambient"))
        {
            ss.clear();
            ss<<technique.child("ambient").child("color").child_value();

            for(int i=0;ss>>temp&&i<4;i++)
            {
                effect.mAmbient.mColor[i]=temp;
            }
        }

        //specular
        if(technique.child("specular"))
        {
            ss.clear();
            ss<<technique.child("specular").child("color").child_value();
            for(int i=0;ss>>temp&&i<4;i++)
            {
                effect.mSpecular.mColor[i]=temp;
            }
        }

        //shininess
        if(technique.child("shininess"))
        {
            ss.clear();
            ss<<technique.child("shininess").child("float").child_value();
            if(!(ss>>temp)) temp=-1;
            effect.mShininess.mValue=temp;
        }

        //transparency
        /*
        ss.clear();
        ss<<technique.child("transparency").child("float").child_value();
        if(!(ss>>temp)) temp=0;
        effect.mTransparency.mValue=temp;
        */

        mEffects.push_back(effect);
    }

}
