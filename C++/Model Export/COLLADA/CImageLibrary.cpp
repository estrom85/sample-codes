#include "CImageLibrary.h"
#include <sstream>
#include <iostream>

void CImageLibrary::load(pugi::xml_node &node)
{



    for(pugi::xml_node image=node.child("image");image;image=image.next_sibling("image"))
    {

        CImageFile img_file;
        img_file.mID=image.attribute("id").value();



        std::stringstream ss;
        std::string temp=image.child("init_from").child_value();


        //std::cout<<image.child("init_from").child_value();


        int start=temp.find_last_of("\\/")+1;

        int end=temp.rfind('\"');
        if(end<start) end=temp.length();

        string filename(&temp[start],&temp[end]);
        //std::cout<<filename;


        img_file.mInitFrom.mFilename=filename;

        mImages.push_back(img_file);

    }
}
