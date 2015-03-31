#include <iostream>
#include <fstream>
#include <cstdio>
#include <algorithm>
#include <iterator>

#include "opencv2/core/core.hpp"
#include "opencv2/features2d/features2d.hpp"
#include "opencv2/highgui/highgui.hpp"
#include "opencv2/calib3d/calib3d.hpp"
#include "opencv2/nonfree/nonfree.hpp"
#include "opencv2/highgui/highgui.hpp"
#include "opencv2/imgproc/imgproc.hpp"

#include "rapidjson/document.h"
#include "rapidjson/filereadstream.h"

#include "base64.h"

const double THRESHOLD = 30000;

using namespace std;
using namespace cv;
using namespace rapidjson;

Size size(640, 480);
static Document jsonDoc;

static const string fileDesc1 = "../cpp/arg2.txt";
static const string fileDesc2 = "../cpp/arg4.txt";

static const string filePoints1 = "../cpp/arg1.txt";
static const string filePoints2 = "../cpp/arg3.txt";

 

void getDataFromJson(const char* json,int size){
    FILE* fp = fopen(json, "r");
    char readBuffer[size];
    FileReadStream is(fp, readBuffer, sizeof(readBuffer));
    
    jsonDoc.ParseStream(is);
    fclose(fp);
}

vector<KeyPoint>* getKeyPointsFromJson(const char* json) {
    
    getDataFromJson(json,65536);
    vector<KeyPoint>* keypoints_monument = new vector<KeyPoint>();
    const Value& array = jsonDoc["keypoints"];

    for (SizeType i = 0; i < array.Size(); i++) {
        const Value& point = array[i];
        KeyPoint toAdd = KeyPoint(point["x"].GetDouble(), point["y"].GetDouble(), point["size"].GetDouble(), point["angle"].GetDouble(), point["response"].GetDouble(), point["octave"].GetInt(), point["class_id"].GetInt());
        keypoints_monument->push_back(toAdd);
    }
    return keypoints_monument;
}

Mat* getMatFromJson(const char* json) {

    getDataFromJson(json,131072);

    // Retrieve the data to decode
    string toDecode = jsonDoc["data"].GetString();
    string toDecodeFiltered;

    // Remove the "\" and replace the " " by "+"
    //std::replace(toDecode.begin(), toDecode.end(), ' ', '+');
    std::remove_copy( toDecode.begin(), toDecode.end(), std::back_inserter(toDecodeFiltered), '\\');
    


    unsigned char* decodedInt = mDecode(toDecodeFiltered);

    
    Mat* mat = new Mat(jsonDoc["rows"].GetInt(), jsonDoc["cols"].GetInt(), jsonDoc["type"].GetInt(), (void*)(decodedInt));
 
    return mat;
}

//function to use for mobile imae detection
//params: keypoints of the source and dest image, descriptors of the two images, the accuracy of the test and the acceptance ratio
double checkDescriptors(vector<KeyPoint> keypoints_object, vector<KeyPoint> keypoints_scene, Mat descriptor1, Mat descriptor2, double accuracy) {

    //-- Step 3: Matching descriptor vectors using FLANN matcher
    BFMatcher matcher(NORM_HAMMING, false);
    std::vector<vector<DMatch > > matches;

    matcher.knnMatch(descriptor1, descriptor2, matches, 2);

    //-- Draw only "good" matches (i.e. whose distance is less than 3*min_dist )
    std::vector< DMatch > good_matches;
    std::vector<Point2f> obj;
    std::vector<Point2f> scene;

    for (int i = 0; i < (int) matches.size(); i++) {
        if (matches[i][0].distance < matches[i][1].distance * accuracy) {
            good_matches.push_back(matches[i][0]);
            obj.push_back(keypoints_object[ matches[i][0].queryIdx ].pt);
            scene.push_back(keypoints_scene[ matches[i][0].trainIdx ].pt);
        }
    }

    //-- Localize the object
    double imgArea = size.height * size.width;
    double areaFound = 0;
    double ratio = 0;

    //need at least 4 match
    if (good_matches.size() >= 4) {
        Mat H = findHomography(obj, scene, CV_RANSAC, 5);

        //-- Get the corners from the image_1 ( the object to be "detected" )
        std::vector<Point2f> obj_corners(4);
        obj_corners[0] = cvPoint(0, 0);
        obj_corners[1] = cvPoint(size.height, 0);
        obj_corners[2] = cvPoint(size.height, size.width);
        obj_corners[3] = cvPoint(0, size.width);
        std::vector<Point2f> scene_corners(4);

        perspectiveTransform(obj_corners, scene_corners, H);
        areaFound = contourArea(scene_corners);
        imgArea = size.height * size.width;
        ratio = areaFound / imgArea;
    }

    return ratio;
}

int main(int argc, char** argv) {
    vector<KeyPoint>* keypoints_monument2 = getKeyPointsFromJson(filePoints2.c_str());
    Mat* descriptor2 = getMatFromJson(fileDesc2.c_str());
    vector<KeyPoint>* keypoints_monument1 = getKeyPointsFromJson(filePoints1.c_str());
    Mat* descriptor1 = getMatFromJson(fileDesc1.c_str());

    double ratio = checkDescriptors(*keypoints_monument1, *keypoints_monument2, *descriptor1, *descriptor2, 0.79);

    cout << ratio;

    delete[] descriptor1->data;
    delete[] descriptor2->data;
    delete keypoints_monument1;
    delete keypoints_monument2;
    delete descriptor1;
    delete descriptor2;
    
    return 0;
}
